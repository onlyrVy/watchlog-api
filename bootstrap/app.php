<?php

use App\Services\ApiResponseService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // "Movie not found" / any missing Eloquent record (e.g. GET
        // /saved-movies/9999 where 9999 doesn't exist) — 404 in our
        // standard shape instead of Laravel's default HTML/JSON.
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponseService::error('Resource not found', 404);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponseService::error('Endpoint not found', 404);
            }
        });

        // "Unauthorized requests" — no/invalid token on a protected route.
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponseService::error('Unauthenticated. Please log in.', 401);
            }
        });

        // Form Request / ValidationException failures — reshape Laravel's
        // default {message, errors} into our standard envelope.
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponseService::error('Validation failed', 422, $e->errors());
            }
        });

        // Catch-all: anything else unhandled (a genuine bug, DB error,
        // etc.) — never leak a stack trace to the client, even in
        // debug mode, since this is an API consumed by Flutter, not
        // a browser rendering Laravel's debug page.
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponseService::error(
                    'Something went wrong on our end. Please try again later.',
                    500
                );
            }
        });
    })->create();