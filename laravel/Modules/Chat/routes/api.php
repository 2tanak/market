<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\Api\UsersController as Users;
use Modules\Chat\Http\Controllers\Api\MessagesController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('users', [Users::class, 'index'])->name('users');
	Route::get('messages/{chat_id}/{created_at}/{newm}/{page?}', [MessagesController::class,'messages'])->name('messages')->where(['page' => '[0-9]+']);
	
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