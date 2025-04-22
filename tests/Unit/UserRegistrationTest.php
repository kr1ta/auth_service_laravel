<?php

use App\Events\UserCreated;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;

uses(DatabaseTransactions::class);

test('registration requires valid data', function () {
    // Отправляем POST-запрос без обязательных полей
    $response = $this->postJson('/api/register', []);

    // Проверяем, что возвращается статус 422 (Unprocessable Entity)
    $response->assertStatus(422);

    // Проверяем, что в ответе содержатся ошибки валидации
    $response->assertJsonValidationErrors(['name', 'email', 'password']);
});

test('user can register successfully', function () {
    // Мокируем событие UserCreated
    Event::fake();

    // Данные для регистрации
    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
    ];

    // Отправляем POST-запрос с корректными данными
    $response = $this->postJson('/api/register', $userData);

    // Проверяем, что статус ответа 200 (OK)
    $response->assertStatus(200);

    // Проверяем, что пользователь создан в базе данных
    $this->assertDatabaseHas('users', [
        'name' => $userData['name'],
        'email' => $userData['email'],
    ]);

    // Получаем созданного пользователя
    $user = User::where('email', $userData['email'])->first();

    // Проверяем, что пароль захеширован
    $this->assertTrue(\Hash::check($userData['password'], $user->password));

    // Проверяем, что событие UserCreated было отправлено
    Event::assertDispatched(UserCreated::class, function ($event) use ($user) {
        return $event->userId === $user->id;
    });

    // Проверяем, что в ответе содержится токен
    $response->assertJsonStructure([
        'access_token',
        'token_type',
    ]);
});
