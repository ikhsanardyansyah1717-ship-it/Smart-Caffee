<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Redirect user yang belum login
        // berdasarkan area aplikasi yang diakses.
        $middleware->redirectGuestsTo(function ($request) {

            if (
                $request->is('owner/*') ||
                $request->is('kitchen/*') ||
                $request->is('kasir/*')
            ) {
                return route('admin.login');
            }

            return route('customer.login');
        });

        // Middleware untuk pengecekan role
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();