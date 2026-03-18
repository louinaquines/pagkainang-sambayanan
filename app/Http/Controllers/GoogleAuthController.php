<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        $role = $request->query('role', 'donor');

        // Store role in a cookie — survives the OAuth redirect on Railway
        // (Railway drops PHP sessions between requests on different instances)
        $cookie = Cookie::make('google_oauth_role', $role, 10); // 10 minutes

        return Socialite::driver('google')
            ->stateless()
            ->redirect()
            ->withCookie($cookie);
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Read role from cookie
            $role = $request->cookie('google_oauth_role', 'donor');

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

            // Clear the cookie
            Cookie::queue(Cookie::forget('google_oauth_role'));

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Google login failed: ' . $e->getMessage());
        }
    }
}