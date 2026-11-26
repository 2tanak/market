<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function (Request $request) {
    return response()->json(['ok' => 'успешно зашли на защищеный роут']);
})->middleware('auth:sanctum');
