<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;
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
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        $this->renderable(function(\Exception $e, Request $request){
            if($request->is('admin*')){
                return;
            }
            if($e instanceof ValidationException){
                $errors = [];
                $e = $this->prepareException($e);
                foreach($e->errors() as $key => $err){
                    $errors = array_merge($errors, $err);
                }
                $statusCode = 422;
                $result = [
                    'data' => [
                        'status' => 'error',
                        'messages' => $errors,
                        'result' => null,
                        'code' => $statusCode,
                    ]
                ];
            }

            if($e instanceof  AuthenticationException || $e instanceof RouteNotFoundException){
                $statusCode = 401;
                $result = [
                    'data' => [
                        'status' => 'error',
                        'messages' => ['احراز هویت نشده اید'],
                        'result' => null,
                        'code' => $statusCode
                    ]
                ];
            }

            if($e instanceof ThrottleRequestsException){
                $statusCode = 429;
                $result = [
                    'data' => [
                        'status' => 'error',
                        'messages' => ['در هر دقیقه میتوانید یک درخواست ارسال کنید'],
                        'result' => null,
                        'code' => $statusCode
                    ]
                ];
            }

            if($e instanceof AccessDeniedHttpException){
                $statusCode = 403;
                $result = [
                    'data' => [
                        'status' => 'error',
                        'messages' => [$e->getMessage()],
                        'result' => null,
                        'code' => $statusCode
                    ]
                ];
            }

            if($e instanceof NotFoundHttpException){
                $statusCode = 404;
                $message = $e->getMessage();
                if(strpos($message, 'No query results for model') === 0){
                    $message = 'داده مورد نظر در دیتابیس یافت نشد';
                }

                $result = [
                    'data' => [
                        'status' => 'error',
                        'messages' => [$message],
                        'result' => null,
                        'code' => $statusCode
                    ]
                ];
            }
            if($e instanceof ServiceUnavailableHttpException){
                $statusCode = 503;
                $result = [
                    'data' => [
                        'status' => 'error',
                        'messages' => [$e->getMessage()],
                        'result' => null,
                        'code' => $statusCode
                    ]
                ];
            }
            if( $e instanceof UnprocessableEntityHttpException){
                $statusCode = 402;
                $result = [
                    'data' => [
                        'status' => 'error',
                        'messages' => [$e->getMessage()],
                        'result' => null,
                        'code' => $statusCode
                    ]
                ];
            }
            if(isset($result)){
                return response()->json($result, $statusCode);
            }

        });
    }

}
