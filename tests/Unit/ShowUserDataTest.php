<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

test('it returns authenticated user data', function () {
    $this->withoutMiddleware(\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class);
    // Создаем тестового пользователя
    $user = \App\Models\User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    // Аутентифицируем пользователя
    $this->actingAs($user, 'sanctum');

    // Выполняем GET-запрос к маршруту /user
    $response = $this->get('/api/user');

    // Проверяем статус ответа
    $response->assertStatus(200);

    // Проверяем структуру JSON-ответа
    $response->assertJson([
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at->toISOString(),
            'updated_at' => $user->updated_at->toISOString(),
        ],
        'errors' => [],
    ]);
});

test('it returns 401 if user is not authenticated', function () {
    // Выполняем GET-запрос к маршруту /user без аутентификации
    $response = $this->get('/api/user');

    // Проверяем, что возвращается статус 401 Unauthorized
    $response->assertStatus(401);

    // Проверяем, что в ответе содержится ожидаемое сообщение об ошибке
    $response->assertJson([
        'message' => 'Please log in to access this resource.',
    ]);
});
