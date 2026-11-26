<?php

use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\Api\UsersController as Users;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('users', [Users::class, 'index'])->name('users');
});

