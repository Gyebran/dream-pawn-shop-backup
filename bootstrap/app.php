<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureMember;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureAdmin::class,
            'member' => EnsureMember::class,
        ]);

        // Trust the proxy headers used to detect HTTPS on Render.
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO,
        );

        // Redirect guests to the correct role-specific login form.
        $middleware->redirectGuestsTo(function (Request $request): ?string {
            if ($request->is('admin', 'admin/*')) {
                return route('admin.login.view');
            }

            if ($request->is('member', 'member/*')) {
                return route('member.login.view');
            }

            return null;
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Browser requests redirect to login; JSON/API requests retain 401 responses.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, Throwable $e) => $request->expectsJson()
        );
    })
    ->create();
