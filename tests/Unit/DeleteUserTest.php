<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;

uses(DatabaseTransactions::class);
test('user can delete their account', function () {
    // $this->withoutMiddleware(\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class);

    $this->withoutExceptionHandling();

    // Создаем пользователя и аутентифицируем его
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    // Выполняем DELETE-запрос к маршруту /user
    $response = $this->delete('/api/user');

    // Проверяем статус ответа
    $response->assertStatus(200);

    // Проверяем содержимое JSON-ответа с новой структурой
    $response->assertJson([
        'data' => [
            'message' => 'Account deleted successfully',
        ],
        'errors' => [],
    ]);

    // Проверяем, что пользователь был удален из базы данных
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});
