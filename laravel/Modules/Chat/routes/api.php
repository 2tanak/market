<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\Api\MessageController;
use Modules\Chat\Http\Controllers\Api\MessagesController;
use Modules\Chat\Http\Controllers\Api\UserController;
use Modules\Chat\Http\Controllers\Api\UsersController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('users', [UsersController::class, 'index'])->name('users');
    Route::get('messages/{chat_id}/{created_at}/{newm}/{page?}', [MessagesController::class, 'messages'])->name('messages')->where(['page' => '[0-9]+']);
    Route::get('user/{user?}', [UserController::class, 'user'])->name('user');

    // отправляем из чата сообщение
    Route::post('message', [MessageController::class, 'message'])->name('message');

    // запускаем очередб статус 1
    Route::post('ping', [MessageController::class, 'forceQueue']);

    // останавливаем очередб статус 2 или 3
    Route::post('ping_success', [MessageController::class, 'updateStatus']);
});

Route::middleware('auth:sanctum')->post('/verify-token', function (Request $request) {
    // Если запрос дошел сюда, значит токен ВАЛИДНЫЙ
    // Достаем юзера, которому принадлежит этот токен
    $user = $request->user();

    // Возвращаем ответ для Node.js
    return response()->json([
        'success' => true,
        'user_id' => $user->id,
        'email' => $user->email, // можно еще почту для логов
    ]);
});
