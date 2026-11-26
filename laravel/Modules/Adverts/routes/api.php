<?php

use Illuminate\Support\Facades\Route;
use Modules\Adverts\Http\Controllers\AdvertsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('adverts', AdvertsController::class)->names('adverts');
});
