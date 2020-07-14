<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $prefix = substr($request->path(),0,3);
        // we only enable this for api call
        if ($prefix === "api")
        {
            if ($exception instanceof Exception)
            {
                if ($exception instanceof NotFoundHttpException)
                {
                    if ($exception->getStatusCode() == 404) {
                        $response = [
                            'success' => false,
                            'message' => __('Method Not Found'),
                            'payload' => [
                                'path' => $request->path(),
                                'message' => $exception->getMessage(),
                                'srcClass' => get_class($exception),
                                'fileTrace' => $exception->getFile(),
                                'lineTrace'      => $exception->getLine(),
                                'codeTrace'      => $exception->getCode(),
                                'stackTrace' => explode("\n",$exception->getTraceAsString())

                            ]
                        ];
                        return response($response, 404);
                    }
                    if ($exception->getStatusCode() == 500) {
                        $response = [
                            'success' => false,
                            'message' => __('Internal Server Error'),
                            'payload' => [
                                'path' => $request->path(),
                                'message' => $exception->getMessage(),
                                'srcClass' => get_class($exception),
                                'fileTrace' => $exception->getFile(),
                                'lineTrace'      => $exception->getLine(),
                                'codeTrace'      => $exception->getCode(),
                                'stackTrace' => explode("\n",$exception->getTraceAsString())

                            ]
                        ];
                        return response($response, 500);
                    }
                } else
                   if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                        $response = [
                            'success' => false,
                            'message' => __($exception->getMessage()),
                            'payload' => [
                                'path' => $request->path(),
                                'message' => $exception->getMessage(),
                                'srcClass' => get_class($exception),
                                'fileTrace' => $exception->getFile(),
                                'lineTrace'      => $exception->getLine(),
                                'codeTrace'      => $exception->getCode(),
                                'stackTrace' => explode("\n",$exception->getTraceAsString())

                            ]
                        ];
                        return response($response, 401);
                } else
                if ($exception instanceof \Illuminate\Database\QueryException) {
                    //1045 Access denied for user
                    // 42S22 Column not found
                    // 1049 unknown database
                        $response = [
                            'success' => false,
                            'message' => __($exception->getMessage()),
                            'payload' => [
                                'path' => $request->path(),
                                'message' => $exception->getMessage(),
                                'srcClass' => get_class($exception),
                                'fileTrace' => $exception->getFile(),
                                'lineTrace'      => $exception->getLine(),
                                'codeTrace'      => $exception->getCode(),
                                'stackTrace' => explode("\n",$exception->getTraceAsString())

                            ]
                        ];
                        return response($response, 401);
                }
                else {
                    //dd($exception);
                    $response = [
                        'success' => false,
                        'message' => __($exception->getMessage()),
                        'payload' => [
                            'path' => $request->path(),
                            'message' => $exception->getMessage(),
                            'srcClass' => get_class($exception),
                            'fileTrace' => $exception->getFile(),
                            'lineTrace'      => $exception->getLine(),
                            'codeTrace'      => $exception->getCode(),
                            'stackTrace' => explode("\n",$exception->getTraceAsString())

                        ]
                    ];
                    return response($response, 500);
                }
            }
        }
        return parent::render($request, $exception);
    }
}
