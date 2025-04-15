<?php

namespace Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use App\Http\Controllers\TokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\Guard;

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Event;
use App\Events\UserCreated;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

test('token is not provided', function () {
    // Выполняем GET-запрос без токена
    $response = $this->getJson('/api/validate-token');

    // Проверяем статус ответа и содержимое JSON
    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Токен не предоставлен',
            'valid' => false,
        ]);
});

test('invalid or expired token', function () {
    // Выполняем GET-запрос с неверным токеном
    $response = $this->withHeaders([
        'Authorization' => 'Bearer invalid-token',
    ])->getJson('/api/validate-token');

    // Проверяем статус ответа и содержимое JSON
    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Неверный или просроченный токен',
            'valid' => false,
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
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Токен валиден',
            'valid' => true,
            'user_id' => $user->id,
        ]);
});


