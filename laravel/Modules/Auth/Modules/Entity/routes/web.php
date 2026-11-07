<?php

use Illuminate\Support\Facades\Route;
use Modules\Entity\Http\Controllers\EntityController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('entities', EntityController::class)->names('entity');
});
