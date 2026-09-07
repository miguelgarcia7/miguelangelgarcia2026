<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo('/admin/login');
        $middleware->redirectUsersTo('/admin/ai-knowledge');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // The login page is plain Blade. When an expired session sends an
        // Inertia request there, Inertia would show the page inside its
        // error modal instead of navigating. A 409 with X-Inertia-Location
        // makes the client do a full page load of the login page.
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            if (! $request->inertia()) {
                return $response;
            }

            if ($exception instanceof AuthenticationException) {
                return Inertia::location(route('admin.login'));
            }

            // An expired CSRF token means the session is gone as well.
            if ($response->getStatusCode() === 419) {
                return Inertia::location(route('admin.login'));
            }

            return $response;
        });
    })->create();
