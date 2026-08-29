<?php

use App\Http\Middleware\EnsureActiveUser;
use App\Http\Middleware\EnsureMasterAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->alias([
            'active.admin' => EnsureActiveUser::class,
            'master.admin' => EnsureMasterAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Production exception customisation can be added here.
    })->create();
