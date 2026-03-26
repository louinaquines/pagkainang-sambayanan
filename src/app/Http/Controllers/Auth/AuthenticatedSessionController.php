<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): \Illuminate\Http\RedirectResponse

    {
        return redirect('/');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
       $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();
        $name = $user->name;
        $role = ucfirst($user->role);

        // Redirect charity users to complete their application if organization_name is NULL
        if ($user->role === 'charity' && empty($user->organization_name)) {
            return redirect()->route('charity.register')
                ->with('login_success', "Welcome back, {$name}! Please complete your organization registration.");
        }

        return redirect()->intended(route('dashboard', absolute: false))
            ->with('login_success', "Welcome back, {$name}! You are logged in as {$role}.");
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
