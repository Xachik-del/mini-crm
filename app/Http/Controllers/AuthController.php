<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:6|confirmed'
            ]);

            /** @var User $user */
            $user = User::query()->create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => "Успех!",
                'user' => $user,
                'token' => $token
            ], 201);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => "Ошибка!",
                'user' => null,
                'token' => null
            ], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            /** @var User $user */
            $user = User::query()
                ->where('email', $request->get('email'))
                ->first();

            if (!$user || !Hash::check($request->get('password'), $user->password)) {
                return response()->json(['message' => 'Неверные учетные данные'], 401);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => "Ошибка!",
                'user' => $user,
                'token' => $token
            ], 200);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => "Ошибка!",
                'user' => null,
                'token' => null
            ], 500);
        }
    }
}

