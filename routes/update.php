<?php

/*
|--------------------------------------------------------------------------
| Update Routes
|--------------------------------------------------------------------------
|
| These routes handle the software update process.
|
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UpdateController;

Route::controller(UpdateController::class)->group(function () {
    Route::get('/', 'update_software_index')->name('index');
    Route::post('/update-system', 'update_software')->name('update-system');
});

Route::fallback(fn () => redirect('/'));
