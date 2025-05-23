<?php

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (QueryException $e, Request $request) {
            return response()->json(['Error' => __('Unprocessable Entity')], 422);
        });

        $exceptions->render(function (RequestException $e, Request $request) {
            return response()->json(['Error' => __('Bad Gateway')], 502);
        });

        $exceptions->render(function (ConnectionException $e, Request $request) {
            return response()->json(['Error' => __('Service Unavailable')], 503);
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return response()->json(['Error' => $e->getMessage()], 404);
        });
    })->create();
