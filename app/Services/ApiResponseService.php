<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

/**
 * Every controller returns responses through here so the JSON shape
 * is identical across all endpoints — Flutter's error handling
 * (no internet, validation errors, unauthorized, etc.) parses one
 * predictable format instead of guessing per-endpoint.
 */
class ApiResponseService
{
    public static function success(mixed $data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function error(string $message = 'Something went wrong', int $status = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}