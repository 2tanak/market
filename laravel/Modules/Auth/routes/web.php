<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\UserController;
use Modules\Auth\Http\Controllers\RolesController;
use Modules\Auth\Http\Controllers\PermissionsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('auths', AuthController::class)->names('auth');
});
 Route::group([], function () {
	  Route::get('login', [AuthController::class, 'index'])->name('login');
		
	  Route::post('check', [AuthController::class, 'check_login'])->name('check_login');
	  
	  Route::any('logout', [AuthController::class, 'logout'])->name('admin_logout');
 });
 
 Route::group(['middleware' => ['auth.admin']], function () {
	Route::resource('user', UserController::class);
	
	Route::resource('role', RolesController::class);
	Route::resource('permission', PermissionsController::class);
});