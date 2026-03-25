<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Force unregistered charities to fill in their application
                if ($user->role === 'charity' && empty($user->organization_name)) {
                    return redirect()->route('charity.register');
                }

                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}