<?php

use Illuminate\Support\Facades\Route;
use Modules\Grafika\Http\Controllers\GrafikaController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('grafikas', GrafikaController::class)->names('grafika');
});
