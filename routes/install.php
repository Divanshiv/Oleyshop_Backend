<?php

/*
|--------------------------------------------------------------------------
| Install Routes
|--------------------------------------------------------------------------
|
| These routes handle the installation process.
|
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallController;

Route::controller(InstallController::class)->group(function () {
    Route::get('/', 'step0')->name('step0');
    Route::get('/step1', 'step1')->name('step1');
    Route::get('/step2', 'step2')->name('step2');

    Route::middleware('installation-check')->group(function () {
        Route::get('/step3/{error?}', 'step3')->name('step3');
        Route::get('/step4', 'step4')->name('step4');
        Route::get('/step5', 'step5')->name('step5');

        Route::post('/database_installation', 'database_installation')->name('install.db');
        Route::get('/import_sql', 'import_sql')->name('import_sql');
        Route::get('/force-import-sql', 'force_import_sql')->name('force-import-sql');
    });

    Route::post('/system_settings', 'system_settings')->name('system_settings');
    Route::post('/purchase_code', 'purchase_code')->name('purchase.code');
});

Route::fallback(fn () => redirect('/'));
