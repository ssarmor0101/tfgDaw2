<?php

use App\Http\Controllers\Api\AmigoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JuegoController;
use App\Http\Controllers\Api\LogroController;
use App\Http\Controllers\Api\PuntuacionController;
use App\Http\Controllers\Api\ResultadoController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');

    Route::name('api.')->group(function () {
        // Amigos
        Route::get('/amigos/user/{user?}', [AmigoController::class, 'getFriendsByUserId'])->name('amigos.user');
        Route::post('/amigos/request', [AmigoController::class, 'requestFriendshipByAuthUser'])->name('amigos.request');
        Route::put('/amigos/{amigo}/accept', [AmigoController::class, 'acceptFriendship'])->name('amigos.accept');
        Route::apiResource('amigos', AmigoController::class);

        // Puntuaciones
        Route::get('/puntuaciones/user/{user?}', [PuntuacionController::class, 'getPuntuacionesByUserId'])->name('puntuaciones.user');
        Route::get('/puntuaciones/juego/{juego}', [PuntuacionController::class, 'getPuntuacionesByAuthUserJuegoId'])->name('puntuaciones.juego');
        Route::get('/puntuaciones/user/{user}/juego/{juego}', [PuntuacionController::class, 'getPuntuacionesByUserIdJuegoId'])->name('puntuaciones.user.juego');
        Route::post('/puntuaciones/juego/{juego}', [PuntuacionController::class, 'publishPuntuacion'])->name('puntuaciones.publish');
        Route::apiResource('puntuaciones', PuntuacionController::class);

        // Resultados
        Route::get('/resultados/user/{user?}', [ResultadoController::class, 'getResultadosByUserId'])->name('resultados.user');
        Route::get('/resultados/juego/{juego}', [ResultadoController::class, 'getResultadosByAuthUserJuegoId'])->name('resultados.juego');
        Route::get('/resultados/friend/{friend}/juego/{juego}', [ResultadoController::class, 'getResultadosByFriendIdJuegoId'])->name('resultados.friend.juego');
        Route::post('/resultados/logro/{logro}', [ResultadoController::class, 'publishResultado'])->name('resultados.publish');
        Route::apiResource('resultados', ResultadoController::class);

        // Users
        Route::delete('/account', [UserController::class, 'eliminateAccount'])->name('users.eliminate');
        Route::apiResource('users', UserController::class);

        // Other Resources
        Route::apiResource('juegos', JuegoController::class);
        Route::apiResource('logros', LogroController::class);
    });
});
