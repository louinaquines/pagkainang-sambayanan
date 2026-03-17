<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        $role = $request->query('role', 'donor');

        // Store role in session — survives the OAuth redirect reliably
        session(['google_role' => $role]);

        // stateless() avoids state mismatch exceptions
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            // stateless() must match what we used in redirect
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Read role from session (set before redirect)
            $role = session('google_role', 'donor');

            $user = User::where('email', $googleUser->email)
                        ->orWhere('google_id', $googleUser->id)
                        ->first();

            if ($user) {
                // Existing user — just update google_id if missing
                $user->update([
                    'google_id'          => $googleUser->id,
                    'email_verified_at'  => $user->email_verified_at ?? now(),
                ]);
            } else {
                // New user — create with selected role
                $user = User::create([
                    'name'               => $googleUser->name,
                    'email'              => $googleUser->email,
                    'google_id'          => $googleUser->id,
                    'password'           => Hash::make(Str::random(16)),
                    'role'               => $role,
                    'email_verified_at'  => now(), // Google already verified the email
                    'verification_status' => $role === 'charity' ? 'pending' : 'approved',
                ]);
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            // Clear the role from session after use
            session()->forget('google_role');

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Google login failed: ' . $e->getMessage());
        }
    }
}