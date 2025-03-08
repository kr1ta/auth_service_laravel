<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\View\View;

class UserController extends Controller
{
  /**
  * Show the profile for a given user.
  */
  public function show(string $id)
{
    $user = User::findOrFail($id);
    $allUsers = User::all();

    // Формируем текстовый ответ
    $text = "User: " . $user->name . "\n";
    $text .= "Success: true\n";
    $text .= "All users: " . $allUsers->pluck('name')->implode(', ');

    return response($text, 200)
        ->header('Content-Type', 'text/plain');
}
}
