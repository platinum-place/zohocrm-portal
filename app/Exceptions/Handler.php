<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
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

        return parent::render($request, $e);
    }
}
