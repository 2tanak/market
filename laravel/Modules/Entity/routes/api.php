<?php

use Illuminate\Support\Facades\Route;
use Modules\Entity\Http\Controllers\EntityController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('entities', EntityController::class)->names('entity');
});
