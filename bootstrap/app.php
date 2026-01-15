<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


return Application::configure(basePath: dirname(__DIR__))

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');  // Trust all proxies
    })
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware alias
        $middleware->alias([
            'whitelist.api' => \App\Http\Middleware\WhitelistStockApi::class,

            'blacklistip' => \App\Http\Middleware\BlacklistIp::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
