<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return success response.
     */
    protected function success(
        $data = null, 
        string $message = 'Success', 
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }
    
    /**
     * Return error response.
     */
    protected function error(
        string $message, 
        int $code = 400, 
        $data = null
    ): JsonResponse {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], $code);
    }
    
    /**
     * Return validation error response.
     */
    protected function validationError($errors): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $errors
        ], 422);
    }
    
    /**
     * Return not found response.
     */
    protected function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, 404);
    }
    
    /**
     * Return unauthorized response.
     */
    protected function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->error($message, 401);
    }
    
    /**
     * Return forbidden response.
     */
    protected function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->error($message, 403);
    }
    
    /**
     * Return created response.
     */
    protected function created($data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }
    
    /**
     * Return no content response.
     */
    protected function noContent(string $message = 'Operation successful'): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => null
        ], 204);
    }
}
