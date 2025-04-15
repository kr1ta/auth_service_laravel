<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);


test('user can update password successfully', function () {
    // Создаем пользователя с начальным паролем
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    // Аутентифицируем пользователя и получаем токен
    $token = $user->createToken('test-token')->plainTextToken;

    // Отправляем запрос на обновление пароля
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->postJson('/api/update-password', [
        'current_password' => 'old-password',
        'new_password' => 'new-password123',
    ]);

    // Проверяем успешный ответ
    $response->assertStatus(200);
    $response->assertJson(['message' => 'Password updated successfully']);

    // Проверяем, что пароль действительно обновлен
    $this->assertTrue(Hash::check('new-password123', $user->fresh()->password));
});


test('user cannot update password with incorrect current password', function () {
    // Создаем пользователя с начальным паролем
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    // Аутентифицируем пользователя и получаем токен
    $token = $user->createToken('test-token')->plainTextToken;

    // Отправляем запрос с неверным текущим паролем
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->postJson('/api/update-password', [
        'current_password' => 'wrong-password',
        'new_password' => 'new-password123',
    ]);

    // Проверяем ошибку с HTTP-статусом 400
    $response->assertStatus(400);
    $response->assertJson(['message' => 'Current password is incorrect']);

    // Проверяем, что пароль не изменился
    $this->assertFalse(Hash::check('new-password123', $user->fresh()->password));
});


test('validation fails when required fields are missing or invalid', function () {
    // Создаем пользователя
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    // Отправляем запрос без `current_password`
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->postJson('/api/update-password', [
        'new_password' => 'short',
    ]);

    // Проверяем ошибки валидации
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['current_password', 'new_password']);
});
