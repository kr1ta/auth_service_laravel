<?php

namespace App\Http\Controllers;

use App\Services\ResponseHelperService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    public function validateToken(Request $request): JsonResponse
    {
        \Log::info('validateToken endpoint hit', [
            'request_data' => $request->all(),
            'headers' => $request->headers->all(), // Вывод всех заголовков
        ]);

        if (! $request->bearerToken()) {
            return ResponseHelperService::error([
                [
                    'code' => 'invalid_token',
                    'message' => 'No token provided',
                ],
            ]);
        }

        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return ResponseHelperService::error([
                [
                    'code' => 'invalid_token',
                    'message' => 'Invalid or expired token',
                ],
            ]);
        }

        return ResponseHelperService::success([
            'valid' => true,
            'user_id' => $user->id,
        ]);
    }
}
