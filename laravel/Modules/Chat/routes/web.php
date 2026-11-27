<?php

use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\ChatController;

Route::group(['middleware' => ['auth.admin']], function () {
    Route::get('chat/{user?}', [ChatController::class, 'chat'])->name('chat');

});
