<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Throwable;

class Handler extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        // ...
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            $code = 500;

            if (method_exists($e, 'getStatusCode')) {
                $code = $e->getStatusCode();
            } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                $code = $e->getStatusCode();
            }

            if ($e instanceof \Illuminate\Http\Client\RequestException) {
                return response()->json([
                    'Error' => __('Internal Server Error').': '.$e->getMessage(),
                    'code' => $code,
                ], $code);
            } elseif ($e instanceof \Illuminate\Http\Client\ConnectionException) {
                return response()->json([
                    'Error' => __('Connection Closed Without Response').': '.$e->getMessage(),
                    'code' => 503,
                ], 503);
            }

            return response()->json([
                'Error' => $e->getMessage(),
                'code' => $code,
            ], $code);
        }

        return false;
    }
}
