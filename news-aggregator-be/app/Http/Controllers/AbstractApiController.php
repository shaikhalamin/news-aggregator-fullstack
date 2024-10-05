<?php

namespace App\Http\Controllers;

class AbstractApiController extends Controller
{
    protected function apiSuccessResponse(mixed $apiResult, int $status = 200)
    {
        $response = [
            'success' => true,
            'data' => $apiResult,
        ];
        return response()->json($response, $status);
    }

    protected function apiErrorResponse(mixed $errorResult, int $status = 404)
    {
        return response()->json($errorResult, $status);
    }
}
