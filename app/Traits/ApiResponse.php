<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return a success JSON response.
     */
    public function successResponse(mixed $data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return an error JSON response.
     */
    public function errorResponse(string $message = 'Error', int $code = 400, mixed $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a validation error JSON response.
     */
    public function validationErrorResponse(mixed $errors, string $message = 'Validation failed'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }

    /**
     * Return an unauthorized JSON response.
     */
    public function unauthorizedResponse(string $message = 'Unauthorized access'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 401);
    }
}
