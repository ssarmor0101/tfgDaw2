<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JuegoController;
use App\Http\Controllers\Api\LogroController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');

Route::middleware('auth:sanctum')->group(function () {

    Route::as('api.')->group(function () {
        Route::get('/juegos', [JuegoController::class, 'index'])->name('juegos.index');
        Route::post('/juegos', [JuegoController::class, 'store'])->name('juegos.store');

        Route::get('/logros', [LogroController::class, 'index'])->name('logros.index');
        Route::post('/logros', [LogroController::class, 'store'])->name('logros.store');

        // Route::apiResource('/juegos', JuegoController::class);

        // Route::apiResources([
        //     'juegos' => JuegoController::class,
        //     'logros' => LogroController::class,
        //     'users' => UserController::class,
        // ]);
    });

});
