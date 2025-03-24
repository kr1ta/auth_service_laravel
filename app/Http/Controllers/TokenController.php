<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    public function validateToken(Request $request)
    {
        if (!$request->bearerToken()) {
            return response()->json([
                'message' => 'Токен не предоставлен',
            ], 401);
        }

        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Неверный или просроченный токен',
                'answer' => 'false',
            ], 401);
        }

        return response()->json([
            'message' => 'Токен валиден',
            'answer' => 'true',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 200);
    }
}