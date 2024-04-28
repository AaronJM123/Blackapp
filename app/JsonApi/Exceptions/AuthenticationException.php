<?php

namespace App\JsonApi\Exceptions;

use Exception;

class AuthenticationException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'errors' => [
                [
                    'title' => 'Unauthenticated',
                    'detail' => 'This action requires authentication.',
                    'status' => '401',
                ],
            ],
        ], 401);
    }
}
