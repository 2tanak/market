<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\StartController;

Route::group(['middleware' => ['auth.admin']], function () {
	Route::prefix('admin')->group(function () {
     Route::resource('blog', StartController::class);
	});
});