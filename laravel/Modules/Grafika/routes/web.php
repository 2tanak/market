<?php

use Illuminate\Support\Facades\Route;
use Modules\Grafika\Http\Controllers\GrafikaController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('grafikas', GrafikaController::class)->names('grafika');
});
