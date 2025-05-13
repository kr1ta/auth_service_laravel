<?php

namespace App\Http\Controllers;

use App\Events\UserCreated;
use App\Models\User;
use App\Services\ResponseHelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        \Log::info('Register endpoint hit', ['request_data' => $request->all()]);

        try {
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

            return ResponseHelperService::success([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (\Exception $e) {
            return ResponseHelperService::error([
                [
                    'code' => 'server_error',
                    'message' => 'Something went wrong during registration.',
                ],
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($credentials)) {
            return ResponseHelperService::error([
                [
                    'code' => 'invalid_input',
                    'message' => 'Invalid email or password.',
                ],
            ]);
        }

        $user = Auth::guard('sanctum')->user();

        $user->tokens()->delete();

        $newToken = $user->createToken('auth_token');

        return ResponseHelperService::success([
            'access_token' => $newToken->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return ResponseHelperService::success(['message' => 'Successfully logged out']);
        } catch (\Exception $e) {
            return ResponseHelperService::error([
                [
                    'code' => 'server_error',
                    'message' => 'Failed to log out user.',
                ],
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6',
            ]);

            $user = $request->user();

            if (! Hash::check($validatedData['current_password'], $user->password)) {
                return ResponseHelperService::error([
                    [
                        'code' => 'invalid_input',
                        'message' => 'Current password is incorrect.',
                    ],
                ]);
            }

            $user->password = Hash::make($validatedData['new_password']);
            $user->save();

            return ResponseHelperService::success(['message' => 'Password updated successfully']);
        } catch (\Exception $e) {
            return ResponseHelperService::error([
                [
                    'code' => 'server_error',
                    'message' => 'Failed to update password.',
                ],
            ], 500);
        }
    }

    public function deleteAccount(Request $request)
    {
        try {
            $user = $request->user();
            $user->tokens()->delete();
            $user->delete();

            return ResponseHelperService::success(['message' => 'Account deleted successfully']);
        } catch (\Exception $e) {
            return ResponseHelperService::error([
                [
                    'code' => 'server_error',
                    'message' => 'Failed to delete account.',
                ],
            ], 500);
        }
    }
}
