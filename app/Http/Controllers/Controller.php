<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function successResponse($data, $messages = null, $statusCode = 200){
        return response()->json([
            'data' => [
                'status' => 'success',
                'messages' => $messages,
                'result' => $data,
                'code' => $statusCode
            ]
        ],$statusCode);
    }

    protected function customErrorResponse($data, $messages, $statusCode){
        return response()->json([
            'data' => [
                'status' => 'error',
                'messages' => $messages,
                'result' => $data,
                'code' => $statusCode
            ]
        ],$statusCode);
    }
}
