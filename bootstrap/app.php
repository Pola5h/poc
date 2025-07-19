<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register Stancl tenancy middleware for tenant identification and database switching
     
        $middleware->append(\Stancl\Tenancy\Middleware\InitializeTenancyByRequestData::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
