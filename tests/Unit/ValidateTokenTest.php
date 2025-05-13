<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

test('token is not provided', function () {
    // Выполняем GET-запрос без токена
    $response = $this->getJson('/api/validate-token');

    // Проверяем статус ответа и содержимое JSON
    $response->assertStatus(400);
    $response->assertJson([
        'data' => null,
        'errors' => [
            [
                'code' => 'invalid_token',
                'message' => 'No token provided',
            ],
        ],
    ]);
});

test('invalid or expired token', function () {
    // Выполняем GET-запрос с неверным токеном
    $response = $this->withHeaders([
        'Authorization' => 'Bearer invalid-token',
    ])->getJson('/api/validate-token');

    // Проверяем статус ответа и содержимое JSON
    $response->assertStatus(400);
    $response->assertJson([
        'data' => null,
        'errors' => [
            [
                'code' => 'invalid_token',
                'message' => 'Invalid or expired token',
            ],
        ],
    ]);
});

test('valid token', function () {
    // Создаем тестового пользователя
    $user = User::factory()->create();

    // Генерируем токен для пользователя
    $token = $user->createToken('test-token')->plainTextToken;

    // Выполняем GET-запрос с валидным токеном
    $response = $this->withHeaders([
        'Authorization' => "Bearer {$token}",
    ])->getJson('/api/validate-token');

    // Проверяем статус ответа и содержимое JSON
    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'valid' => 'true',
            'user_id' => $user->id,
        ],
        'errors' => [],
    ]);
});
