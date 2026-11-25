<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\LanguageController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {
    Route::post('auth/register', [AuthController::class, 'register'])->name('auth.register');

    Route::get('languages', [LanguageController::class, 'index'])->name('languages.index');
});
