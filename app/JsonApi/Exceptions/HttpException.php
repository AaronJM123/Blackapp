<?php

namespace App\JsonApi\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException as BaseHttpException;

class HttpException extends BaseHttpException
{
    public function __construct(BaseHttpException $e)
    {
        parent::__construct($e->getStatusCode(), $e->getMessage());
    }

    public function render($request): JsonResponse
    {
        $detail = method_exists($this, $method = "get{$this->getStatusCode()}Detail")
           ? $this->{$method}($request)
           : $this->getMessage();

        return response()->json([
            'errors' => [[
                'title' => Response::$statusTexts[$this->getStatusCode()],
                'detail' => $detail,
                'status' => (string) $this->getStatusCode(),
            ]],
        ], $this->getStatusCode());
    }

    protected function get404Detail($request): string
    {
        if (str($this->getMessage())->startsWith('No query results for model')) {
            return "No records found with the id '{$request->getResourceId()}'".
                " in the '{$request->getResourceType()}' resource.";
        }

        return $this->getMessage();
    }
}
