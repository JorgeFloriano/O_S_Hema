<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // Adicione este bloco abaixo:
        then: function () {
            Route::middleware('web') // Aplica sessões, CSRF, etc.
                ->prefix('client')  // (Opcional) Adiciona 'client/' na URL
                ->name('client.')   // Adiciona o prefixo 'client.' no nome das rotas
                ->group(base_path('routes/client.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
         // Add CORS middleware
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
        
        // Configure CORS
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
    $app->register(\Barryvdh\DomPDF\ServiceProvider::class);
    $app->configure('dompdf');

