<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\{AuthenticationException, Access\AuthorizationException};
use Symfony\Component\HttpKernel\Exception\{NotFoundHttpException, MethodNotAllowedHttpException, AccessDeniedHttpException, TooManyRequestsHttpException};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use App\Http\Middleware\{SetLocaleMiddleware, SetCountryMiddleware, RoleMiddleware};

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            SetLocaleMiddleware::class,
            SetCountryMiddleware::class
        ]);
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => __('message.unauthenticated'),
                    'data'    => null,
                ], 401);
            }
            return null;
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*', 'admin/*')) {
                return true;
            }

            return $request->expectsJson();
        });

        $renderJson = function ($message, $code, $e) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'data'    => config('app.debug') ? $e->getMessage() : null,
            ], $code);
        };

        // 404 - Not Found
        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($renderJson) {
            if ($request->is('api/*', 'admin/*')) {
                return $renderJson(__('message.page_not_found'), 404, $e);
            }
        });

        // 404 - Model Not Found
        $exceptions->render(function (ModelNotFoundException $e, Request $request) use ($renderJson) {
            if ($request->is('api/*', 'admin/*')) {
                return $renderJson(__('message.record_not_found'), 404, $e);
            }
        });

        // 405 - Method Not Allowed
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) use ($renderJson) {
            if ($request->is('api/*', 'admin/*')) {
                return $renderJson(__('message.method_not_allowed'), 405, $e);
            }
        });

        // 422 - Validation Error
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*', 'admin/*')) {
                return response()->json([
                    'success' => false,
                    'message' => collect($e->errors())->flatten()->first() ?: __('message.validation_failed'),
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // 401 - Authentication Error
        $exceptions->render(function (AuthenticationException $e, Request $request) use ($renderJson) {
            if ($request->is('api/*', 'admin/*')) {
                return $renderJson('Unauthenticated.', 401, $e);
            }
        });

        // 403 - Authorization Error
        $exceptions->render(function (AuthorizationException $e, Request $request) use ($renderJson) {
            if ($request->is('api/*', 'admin/*')) {
                return $renderJson(__('message.access_forbidden'), 403, $e);
            }
        });

        // 403 - Access Denied
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) use ($renderJson) {
            if ($request->is('api/*', 'admin/*')) {
                return $renderJson(__('message.access_denied'), 403, $e);
            }
        });

        // 429 - Too Many Requests
        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request) use ($renderJson) {
            if ($request->is('api/*', 'admin/*')) {
                return $renderJson(__('message.rate_limit_exceeded'), 429, $e);
            }
        });

        // Database Query Exception
        $exceptions->render(function (QueryException $e, Request $request) use ($renderJson) {
            if ($request->is('api/*', 'admin/*')) {
                return $renderJson(__('message.database_error'), 500, $e);
            }
        });

        $exceptions->render(function (\Exception $e, Request $request) use ($renderJson) {
            if ($request->is('api/*', 'admin/*')) {
                return $renderJson(__('message.something_went_wrong'), 500, $e);
            }
        });
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->command('app:update-currency-rates')->daily();
    })->create();
