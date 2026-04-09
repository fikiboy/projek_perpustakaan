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
    ->withMiddleware(function (Middleware $middleware) {
        // Daftarkan alias middleware di sini agar bisa dipanggil di web.php
        $middleware->alias([
            'checkRole' => \App\Http\Middleware\CheckRole::class,
            'checkSuspended' => \App\Http\Middleware\CheckSuspended::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();