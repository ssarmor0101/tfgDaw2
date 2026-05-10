<?php

use App\Http\Controllers\Api\AmigoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JuegoController;
use App\Http\Controllers\Api\LogroController;
use App\Http\Controllers\Api\PuntuacionController;
use App\Http\Controllers\Api\ResultadoController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Public Routes
    |--------------------------------------------------------------------------
    */
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

    /*
    |--------------------------------------------------------------------------
    | Protected Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');

        // Amigos
        Route::get('/amigos/user/{user?}', [AmigoController::class, 'getFriendsByUserId'])->name('amigos.user');
        Route::post('/amigos/request', [AmigoController::class, 'requestFriendshipByAuthUser'])->name('amigos.request');
        Route::put('/amigos/{amigo}/accept', [AmigoController::class, 'acceptFriendship'])->name('amigos.accept');
        Route::get('/amigos', [AmigoController::class, 'index'])->name('amigos.index');
        Route::post('/amigos', [AmigoController::class, 'store'])->name('amigos.store');
        Route::get('/amigos/{amigo}', [AmigoController::class, 'show'])->name('amigos.show');
        Route::match(['put', 'patch'], '/amigos/{amigo}', [AmigoController::class, 'update'])->name('amigos.update');
        Route::delete('/amigos/{amigo}', [AmigoController::class, 'destroy'])->name('amigos.destroy');

        // Puntuaciones
        Route::get('/puntuaciones/user/{user?}', [PuntuacionController::class, 'getPuntuacionesByUserId'])->name('puntuaciones.user');
        Route::get('/puntuaciones/juego/{juego}', [PuntuacionController::class, 'getPuntuacionesByAuthUserJuegoId'])->name('puntuaciones.juego');
        Route::get('/puntuaciones/user/{user}/juego/{juego}', [PuntuacionController::class, 'getPuntuacionesByUserIdJuegoId'])->name('puntuaciones.user.juego');
        Route::post('/puntuaciones/juego/{juego}', [PuntuacionController::class, 'publishPuntuacion'])->name('puntuaciones.publish');
        Route::get('/puntuaciones', [PuntuacionController::class, 'index'])->name('puntuaciones.index');
        Route::post('/puntuaciones', [PuntuacionController::class, 'store'])->name('puntuaciones.store');
        Route::get('/puntuaciones/{puntuacion}', [PuntuacionController::class, 'show'])->name('puntuaciones.show');
        Route::match(['put', 'patch'], '/puntuaciones/{puntuacion}', [PuntuacionController::class, 'update'])->name('puntuaciones.update');
        Route::delete('/puntuaciones/{puntuacion}', [PuntuacionController::class, 'destroy'])->name('puntuaciones.destroy');

        // Resultados
        Route::get('/resultados/user/{user?}', [ResultadoController::class, 'getResultadosByUserId'])->name('resultados.user');
        Route::get('/resultados/juego/{juego}', [ResultadoController::class, 'getResultadosByAuthUserJuegoId'])->name('resultados.juego');
        Route::get('/resultados/friend/{friend}/juego/{juego}', [ResultadoController::class, 'getResultadosByFriendIdJuegoId'])->name('resultados.friend.juego');
        Route::post('/resultados/logro/{logro}', [ResultadoController::class, 'publishResultado'])->name('resultados.publish');
        Route::get('/resultados', [ResultadoController::class, 'index'])->name('resultados.index');
        Route::post('/resultados', [ResultadoController::class, 'store'])->name('resultados.store');
        Route::get('/resultados/{resultado}', [ResultadoController::class, 'show'])->name('resultados.show');
        Route::match(['put', 'patch'], '/resultados/{resultado}', [ResultadoController::class, 'update'])->name('resultados.update');
        Route::delete('/resultados/{resultado}', [ResultadoController::class, 'destroy'])->name('resultados.destroy');

        // Users
        Route::delete('/account', [UserController::class, 'eliminateAccount'])->name('users.eliminate');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::match(['put', 'patch'], '/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Otros recursos
        Route::get('/juegos', [JuegoController::class, 'index'])->name('juegos.index');
        Route::post('/juegos', [JuegoController::class, 'store'])->name('juegos.store');
        Route::get('/juegos/{juego}', [JuegoController::class, 'show'])->name('juegos.show');
        Route::match(['put', 'patch'], '/juegos/{juego}', [JuegoController::class, 'update'])->name('juegos.update');
        Route::delete('/juegos/{juego}', [JuegoController::class, 'destroy'])->name('juegos.destroy');

        Route::get('/logros', [LogroController::class, 'index'])->name('logros.index');
        Route::post('/logros', [LogroController::class, 'store'])->name('logros.store');
        Route::get('/logros/{logro}', [LogroController::class, 'show'])->name('logros.show');
        Route::match(['put', 'patch'], '/logros/{logro}', [LogroController::class, 'update'])->name('logros.update');
        Route::delete('/logros/{logro}', [LogroController::class, 'destroy'])->name('logros.destroy');
    });
});
