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
        $role   = $request->query('role', 'donor');
        $intent = $request->query('intent', 'login'); // 'login' or 'register'

        // Generate a unique token to store role + intent
        $token = Str::random(40);

        Cache::put('google_oauth_' . $token, [
            'role'   => $role,
            'intent' => $intent,
        ], now()->addMinutes(10));

        return Socialite::driver('google')
            ->stateless()
            ->with(['state' => $token])
            ->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Retrieve role + intent from cache
            $token  = $request->input('state');
            $data   = Cache::pull('google_oauth_' . $token, ['role' => 'donor', 'intent' => 'login']);
            $role   = in_array($data['role'] ?? '', ['donor', 'charity']) ? $data['role'] : 'donor';
            $intent = $data['intent'] ?? 'login';

            // SECURITY GUARD: Never allow 'admin' role from Google OAuth
            if (!in_array($role, ['donor', 'charity'])) {
                $role = 'donor';
            }

            \Illuminate\Support\Facades\Log::info("Google OAuth Callback: Email=" . $googleUser->email . ", Intended Role=" . $role . ", Intent=" . $intent);

            $user = User::where('email', $googleUser->email)
                        ->orWhere('google_id', $googleUser->id)
                        ->first();

            if ($intent === 'login') {
                // LOGIN — existing users only
                if (!$user) {
                    \Illuminate\Support\Facades\Log::warning("Google Social Login failed: No account for " . $googleUser->email);
                    return redirect('/')->with('error', 'No account found for this Google account. Please register first.');
                }

                $user->update([
                    'google_id'         => $googleUser->id,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);

            } else {
                // REGISTER — create new or log in existing
                if ($user) {
                    \Illuminate\Support\Facades\Log::info("Google Social Register: User already exists for " . $googleUser->email . " (Current Role: " . $user->role . ")");
                    $user->update([
                        'google_id'         => $googleUser->id,
                        'email_verified_at' => $user->email_verified_at ?? now(),
                    ]);
                } else {
                    \Illuminate\Support\Facades\Log::info("Google Social Register: Creating new user " . $googleUser->email . " as " . $role);
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
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            \Illuminate\Support\Facades\Log::info("Google Auth Success: User ID=" . $user->id . ", Final Role=" . $user->role);

            // Role-based redirect after Google login
            if ($user->role === 'charity' && empty($user->organization_name)) {
                return redirect()->route('charity.register');
            }

            if ($user->role === 'donor') {
                return redirect()->route('dashboard');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Google OAuth Exception: " . $e->getMessage());
            return redirect('/')->with('error', 'Google login failed: ' . $e->getMessage());
        }
    }
}