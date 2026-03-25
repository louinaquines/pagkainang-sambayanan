<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn() => route('welcome'));
        $middleware->redirectUsersTo(function ($request) {
            $user = auth()->user();
            if (!$user) return route('welcome');
            if ($user->role === 'charity' && empty($user->organization_name)) {
                return route('charity.register');
            }
            return route('dashboard');
        });
        $middleware->alias([
            'role'               => \App\Http\Middleware\RoleMiddleware::class,
            'charity.registered' => \App\Http\Middleware\EnsureCharityIsRegistered::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
