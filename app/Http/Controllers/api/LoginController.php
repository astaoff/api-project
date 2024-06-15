<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class LoginController extends Controller
{
    /**
     * Регистрация нового пользователя.
     */
    public function register(Request $request)
    {
        try {
            // Валидация данных
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'sometimes|in:1'
            ]);
    
            // Создание пользователя
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role' => $validatedData['role'] ?? null
            ]);
    
            // Установка роли 'teacher', если требуется
            if (isset($validatedData['role']) && $validatedData['role'] == 1) {
                $user->role = 'teacher';
                $user->save();
            }
    
            // Создание токена аутентификации
            $token = $user->createToken('auth_token')->plainTextToken;
    
            // Успешный ответ
            return response()->json([
                'message' => 'Registration successful',
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Ответ с ошибками валидации
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        }
    }
    /**
     * Авторизация пользователя и получение токена.
     */
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        $user = User::where('email', $validatedData['email'])->first();
    
        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response()->json(['message' => 'Login failed'], 401);
        }
    
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'message' => "Good day, $user->name",
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 200);
        
    }
    //Выход из аккаунта
    public function logout(Request $request)
    {
    $token = $request->user()->currentAccessToken();
    $token->delete();
    return response()->json(['message' => 'Token deleted'], 200);
    }

    public function test(){
        return('Вы вошли');
    }
}