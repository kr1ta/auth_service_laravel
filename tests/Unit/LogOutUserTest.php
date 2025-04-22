<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

test('user can logout and tokens are deleted', function () {
    // Создаем пользователя и аутентифицируем его
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    // Добавляем заголовок авторизации с токеном
    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$token,
    ])->postJson('/api/logout');

    // Проверяем, что ответ имеет статус 200
    $response->assertStatus(200);

    // Проверяем, что возвращается корректное сообщение
    $response->assertJson([
        'message' => 'Successfully logged out',
    ]);

    // Проверяем, что все токены пользователя удалены
    $this->assertCount(0, $user->tokens);
});
