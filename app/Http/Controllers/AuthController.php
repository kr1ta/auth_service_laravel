<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Events\UserCreated;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        \Log::info("Dispatching UserCreated event for user ID: {$user->id}");
        event(new UserCreated($user->id));

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::guard('sanctum')->user();

        $user->tokens()->delete();

        $newToken = $user->createToken('auth_token');

        return response()->json([
            'access_token' => $newToken->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        // Удаляем все токены пользователя
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function updatePassword(Request $request)
    {
        $validatedData = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        $user = $request->user();

        if (!Hash::check($validatedData['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 400);
        }

        $user->password = Hash::make($validatedData['new_password']);
        $user->save();

        return response()->json(['message' => 'Password updated successfully']);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete(); // Удаляем все токены
        $user->delete(); // Удаляем пользователя

        return response()->json(['message' => 'Account deleted successfully']);
    }
}