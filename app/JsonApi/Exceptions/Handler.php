<?php

namespace App\JsonApi\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use App\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\JsonApi\Http\Responses\JsonApiValidationErrorResponse;

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
        $this->renderable(function (HttpException $e, Request $request) {
            $request->isJsonApi() && throw new \App\JsonApi\Exceptions\HttpException($e);
        })->renderable(function (AuthenticationException $e, Request $request) {
            $request->isJsonApi() && throw new \App\JsonApi\Exceptions\AuthenticationException;
        });

        parent::register();
    }

    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        return $request->isJsonApi()
            ? new JsonApiValidationErrorResponse($exception)
            : parent::invalidJson($request, $exception);
    }
}
