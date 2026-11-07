<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminController;

Route::group(['middleware' => ['auth.admin']], function () {
	Route::prefix('admin')->group(function () {
		Route::get('/', [AdminController::class,'index'])->name('admin_index');
	});
});
