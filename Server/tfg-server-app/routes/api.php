<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JuegoController;
use App\Http\Controllers\Api\LogroController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/juegos', [JuegoController::class, 'index'])->name('api.juegos.index');
    Route::post('/juegos', [JuegoController::class, 'store'])->name('api.juegos.store');

    Route::get('/logros', [LogroController::class, 'index'])->name('api.logros.index');
    Route::post('/logros', [LogroController::class, 'store'])->name('api.logros.store');

    // Route::apiResource('/juegos', JuegoController::class);

    // Route::apiResources([
    //     'juegos' => JuegoController::class,
    //     'logros' => LogroController::class,
    //     'users' => UserController::class,
    // ]);

});
