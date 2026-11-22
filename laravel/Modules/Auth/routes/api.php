<?php

use Illuminate\Support\Facades\Route;

use Modules\Auth\Http\Controllers\Api\AuthController;


   Route::post('login', [AuthController::class,'login'])->name('login');
 
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post('/verify-token', function (Request $request) {
    // Если запрос дошел сюда, значит токен ВАЛИДНЫЙ
    // Достаем юзера, которому принадлежит этот токен
    $user = $request->user();

    // Возвращаем ответ для Node.js
    return response()->json([
        'success' => true,
        'user_id' => $user->id,
        'email'   => $user->email // можно еще почту для логов
    ]);
});