<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function successResponse($message, $data =[], $extras = [], $statusCode = 200)
    {
        $body = [
            "data" => $data,
            ...$extras
        ];

        return response()->json($body, $statusCode);
    }
    public function failureResponse($error, $errors = [], $extras = [], $statusCode = 500)
    {
        $body = [
            "errors" => $errors,
            ...$extras
        ];

        return response()->json($body, $statusCode);
    }
}
