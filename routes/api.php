<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/register', function (Request $request) {

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



});

Route::post('/login', function (Request $request) {
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

});

Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('tasks', TaskController::class);
});


