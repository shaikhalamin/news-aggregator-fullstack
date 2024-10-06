<?php

namespace App\Http\Controllers;

class AbstractApiController extends Controller
{
    protected function apiSuccessResponse(mixed $apiResult, int $status = 200)
    {
        $response = [
            'success' => true,
            'data' => $apiResult
        ];

        return response()->json($response, $status);
    }

    protected function apiErrorResponse(array $errors, int $status = 404)
    {
        $response = [
            'success' => false,
            ...$errors
        ];

        return response()->json($response, $status);
    }
}
