<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;

uses(DatabaseTransactions::class);

test('user can login with valid credentials', function () {
    // Создаем пользователя в тестовой базе данных
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    // Делаем POST-запрос к /api/login с правильными учетными данными
    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    // Проверяем успешный ответ (статус 200)
    $response->assertStatus(200);

    // Проверяем структуру JSON-ответа
    $response->assertJson([
        'data' => [
            'access_token' => true, // просто проверяем существование
            'token_type' => 'Bearer',
        ],
        'errors' => [],
    ]);

    // Проверяем, что токен существует в БД
    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'tokenable_type' => User::class,
    ]);
});

test('user cannot login with invalid credentials', function () {
    // Создаем пользователя в тестовой базе данных
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    // Делаем POST-запрос к /login с неправильными учетными данными
    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(400);

    // Проверяем сообщение об ошибке
    $response->assertJson([
        'data' => null,
        'errors' => [
            [
                'code' => 'invalid_input',
                'message' => 'Invalid email or password.',
            ],
        ],
    ]);
});

test('login request validation fails without required fields', function () {
    // Делаем POST-запрос без обязательных полей
    $response = $this->postJson('/api/login', []);

    // Проверяем ответ с ошибками валидации (статус 422)
    $response->assertStatus(422);

    // Проверяем наличие ошибок валидации для email и password
    $response->assertJsonValidationErrors(['email', 'password']);
});
