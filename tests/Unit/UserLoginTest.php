<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

test('user can login with valid credentials', function () {
    // Создаем пользователя в тестовой базе данных
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    // Делаем POST-запрос к /login с правильными учетными данными
    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    // Проверяем успешный ответ (статус 200)
    $response->assertStatus(200);

    // Проверяем структуру ответа
    $response->assertJsonStructure([
        'access_token',
        'token_type'
    ]);

    // Проверяем тип токена
    $response->assertJsonFragment([
        'token_type' => 'Bearer'
    ]);

    // Проверяем, что токен действительно существует в базе данных
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

    // Проверяем ответ с ошибкой авторизации (статус 401)
    $response->assertStatus(401);

    // Проверяем сообщение об ошибке
    $response->assertJson([
        'message' => 'Unauthorized'
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