<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        $role = $request->query('role', 'donor');

        // Generate a unique token to store the role
        $token = Str::random(40);

        // Store role in cache for 10 minutes using the token as key
        Cache::put('google_oauth_role_' . $token, $role, now()->addMinutes(10));

        // Pass token through Google's state parameter
        return Socialite::driver('google')
            ->stateless()
            ->with(['state' => $token])
            ->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Retrieve role from cache using the token in state
            $token = $request->input('state');
            $role = Cache::pull('google_oauth_role_' . $token, 'donor');

            // Validate role
            if (!in_array($role, ['donor', 'charity'])) {
                $role = 'donor';
            }

            $user = User::where('email', $googleUser->email)
                        ->orWhere('google_id', $googleUser->id)
                        ->first();

            if ($user) {
                // Existing user — update google_id, ensure email verified
                $user->update([
                    'google_id'         => $googleUser->id,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            } else {
                // New user — create with selected role
                $user = User::create([
                    'name'                => $googleUser->name,
                    'email'               => $googleUser->email,
                    'google_id'           => $googleUser->id,
                    'password'            => Hash::make(Str::random(16)),
                    'role'                => $role,
                    'email_verified_at'   => now(),
                    'verification_status' => 'unsubmitted',
                ]);
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Google login failed: ' . $e->getMessage());
        }
    }
}