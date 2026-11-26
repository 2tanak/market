<?php

use Illuminate\Support\Facades\Route;
use Modules\Adverts\Http\Controllers\AdvertsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('adverts', AdvertsController::class)->names('adverts');
});
