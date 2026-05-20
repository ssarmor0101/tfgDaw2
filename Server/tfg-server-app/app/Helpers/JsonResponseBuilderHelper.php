<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class JsonResponseBuilderHelper
{
    /**
     * Build a generic JSON response.
     *
     * @param bool $success
     * @param string $message
     * @param array $payload
     * @param int $code
     * @return JsonResponse
     */
    public static function buildJson(bool $success, string $message = '', array $payload = [], int $code = 200): JsonResponse
    {
        return response()->json(collect($payload)->merge([
            'status' => [
                'success' => $success,
                'message' => $message,
            ]
        ])->toArray(), $code);
    }

    /**
     * Build a success JSON response.
     *
     * @param string $message
     * @param array $payload
     * @return JsonResponse
     */
    public static function buildJsonSuccess(string $message = 'Success', array $payload = []): JsonResponse
    {
        return self::buildJson(true, $message, $payload);
    }

    /**
     * Build an error JSON response.
     *
     * @param string $message
     * @param int $code
     * @param array $payload
     * @return JsonResponse
     */
    public static function buildJsonError(string $message = 'Error', int|string $code = 500, array $payload = []): JsonResponse
    {
        $httpCode = is_int($code) && $code >= 100 && $code < 600 ? $code : 500;
        return self::buildJson(false, $message, $payload, $httpCode);
    }
}
