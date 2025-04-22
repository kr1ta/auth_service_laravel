<?php

namespace App\Http\Controllers;

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
            return response()->json([
                'message' => 'Токен не предоставлен',
                'valid' => false,
            ], 401);
        }

        $user = Auth::guard('sanctum')->user();
        if (! $user) {
            return response()->json([
                'message' => 'Неверный или просроченный токен',
                'valid' => false,
            ], 401);
        }

        return response()->json([
            'message' => 'Токен валиден',
            'valid' => true,
            'user_id' => $user->id,
        ], 200);
    }
}
