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
        if ($request->has('role')) {
            session(['google_preferred_role' => $request->role]);
        }
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Find existing user
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // Existing user: Just log them in
                Auth::login($user);
            } else {
                // NEW USER: Get the role we saved in the session
                // Default to 'donor' if for some reason the session is empty
                $role = session('google_preferred_role', 'donor');

                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(Str::random(16)),
                    'role' => $role, // <--- This is the key fix!
                ]);

                Auth::login($newUser);
                
                // Clean up the session
                session()->forget('google_preferred_role');
            }

            return redirect()->intended('dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Something went wrong with Google sign-in.');
        }
    }
}