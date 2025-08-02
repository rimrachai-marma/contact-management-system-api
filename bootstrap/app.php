<?php

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prependToGroup('api', ForceJsonResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            // Handle exceptions for API routes only
            if ($request->is('api/*')) {
                // Unauthenticated (Sanctum or auth middleware)
                if ($e instanceof AuthenticationException) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $request->bearerToken() ? 'Unauthenticated, token failed' : 'Unauthenticated, no token',
                    ], 401);
                }

                // Forbidden (lacking permission)
                if ($e instanceof AuthorizationException) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Forbidden',
                    ], 403);
                }
    

                // Not found
                // if ($e instanceof NotFoundHttpException) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Resource not found',
                //     ], 404);
                // }

                if ($e instanceof NotFoundHttpException) {
                    $previous = $e->getPrevious();

                    // Handle model not found exceptions
                    if ($previous instanceof ModelNotFoundException) {
                        $model = Str::headline(class_basename($previous->getModel()));
                        $ids = implode(', ', $previous->getIds() ?: ['unknown']);

                        return response()->json([
                            'status' => 'error',
                            'message' => "{$model} not found for ID: {$ids}",
                        ], 404);
                    }

                    // Handle route not found
                    return response()->json([
                        'status' => 'error',
                        'message' => "Route not found for route - {$request->path()}",
                    ], 404);
                }

                // Validation errors
                if ($e instanceof ValidationException) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Validation failed',
                        'errors' => $e->errors(),
                    ], 422);
                }

                // Fallback for other exceptions
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage() ?: 'An unexpected error occurred',
                ], ($e instanceof HttpExceptionInterface) ? $e->getStatusCode() : 500);
            }

            // Let Laravel handle other cases (e.g., web routes)
            return null;
        });
    })->create();
