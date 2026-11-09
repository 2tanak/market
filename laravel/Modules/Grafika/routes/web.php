<?php

use Illuminate\Support\Facades\Route;
use Modules\Grafika\Http\Controllers\GrafikaController;
use Modules\Grafika\Http\Controllers\CharController;

Route::middleware(['auth', 'verified'])->group(function () {
   
	Route::prefix('grafika')->group(function() {
		//char линейный
       //для запроса аяксом линейного
	   Route::get('/char', [CharController::class, 'char'])->name('char');
	   //показ графика
       Route::get('charline', [CharController::class, 'charline'])->name('charline');
		 
	});
	
	
	
	
});
