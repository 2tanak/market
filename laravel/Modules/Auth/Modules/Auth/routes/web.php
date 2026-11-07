<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('auths', AuthController::class)->names('auth');
});
 Route::group([], function () {
	  Route::get('login', [AuthController::class, 'index'])->name('login');
		
	  Route::post('check', [AuthController::class, 'check_login'])->name('check_login');
		
 });