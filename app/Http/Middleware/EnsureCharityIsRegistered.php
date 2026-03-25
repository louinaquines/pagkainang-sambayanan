<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCharityIsRegistered extends Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (
            $user &&
            $user->role === 'charity' &&
            empty($user->organization_name) &&
            !$request->routeIs('charity.register') &&
            !$request->routeIs('charity.submit') &&
            !$request->routeIs('profile.*') &&
            !$request->routeIs('logout*')
        ) {
            return redirect()->route('charity.register')
                ->with('info', 'Please complete your organization registration first.');
        }

        return $next($request);
    }
}