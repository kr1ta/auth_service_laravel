<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {

        // Возвращаем JSON с информацией о необходимости авторизации
        abort(response()->json([
            'message' => 'Please log in to access this resource.',
            'error' => 'Unauthenticated.',
        ], 401));

    }
}
