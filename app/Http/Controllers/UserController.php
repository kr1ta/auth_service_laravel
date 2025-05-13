<?php

namespace App\Http\Controllers;

use App\Services\ResponseHelperService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return ResponseHelperService::error([
                [
                    'code' => 'not_found',
                    'message' => 'User not found',
                ],
            ], 404);
        }

        return ResponseHelperService::success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }
}
