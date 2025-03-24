<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('tokens:clear', function () {
    PersonalAccessToken::query()->delete();
    $this->info('All personal access tokens have been deleted.');
})->purpose('__my: clears all sanctum tokens');


Artisan::command('tokens:list', function () {
    $tokens = PersonalAccessToken::all();

    if ($tokens->isEmpty()) {
        $this->info('No personal access tokens found.');
        return;
    }

    $this->table(
        ['ID', 'Tokenable Type', 'Tokenable ID', 'Name', 'Token'], // Заголовки столбцов
        $tokens->map(function ($token) {
            return [
                $token->id,
                $token->tokenable_type,
                $token->tokenable_id,
                $token->name,
                $token->token,
            ];
        })->toArray()
    );
})->purpose('__my: lists all sanctum tokens');