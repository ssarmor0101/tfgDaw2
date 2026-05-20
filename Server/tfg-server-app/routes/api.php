<?php

use App\Http\Controllers\Api\AmigoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
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

    // Rutas de consulta pública (Usuario Común Anónimo)
    Route::get('/juegos', [JuegoController::class, 'index'])->name('juegos.index');
    Route::get('/juegos/populares', [JuegoController::class, 'popular'])->name('juegos.popular');
    Route::get('/juegos/recientes', [JuegoController::class, 'recent'])->name('juegos.recent');
    Route::get('/juegos/buscar', [JuegoController::class, 'buscar'])->name('juegos.buscar');
    Route::get('/juegos/{juego}', [JuegoController::class, 'show'])->name('juegos.show');

    Route::get('/logros', [LogroController::class, 'index'])->name('logros.index');
    Route::get('/logros/juego/{juego}', [LogroController::class, 'getLogrosByJuegoId'])->name('logros.juego');
    Route::get('/logros/{logro}', [LogroController::class, 'show'])->name('logros.show');

    Route::get('/puntuaciones', [PuntuacionController::class, 'index'])->name('puntuaciones.index');
    Route::get('/puntuaciones/juego/{juego}', [PuntuacionController::class, 'getPuntuacionesByJuegoId'])->name('puntuaciones.juego');
    Route::get('/puntuaciones/user/{user}/juego/{juego}', [PuntuacionController::class, 'getPuntuacionesByUserIdJuegoId'])->name('puntuaciones.user.juego');

    Route::get('/resultados/user/{user}/juego/{juego}', [ResultadoController::class, 'getResultadosByUserIdJuegoId'])->name('resultados.user.juego');

    Route::get('/users/search/{name}', [UserController::class, 'searchUsersByName'])->name('users.search');

    /*
    |--------------------------------------------------------------------------
    | Authenticated Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

        // Cuenta (Acciones personales)
        Route::get('/account', [UserController::class, 'getAccount'])->name('users.getAccount');
        Route::match(['put', 'patch'], '/account', [UserController::class, 'updateOwnProfile'])->name('users.updateOwnProfile');
        Route::delete('/account', [UserController::class, 'eliminateAccount'])->name('users.eliminate');

        // Puntuaciones personales y publicación
        Route::get('/puntuaciones/me/juego/{juego}', [PuntuacionController::class, 'getPuntuacionesByAuthUserJuegoId'])->name('puntuaciones.me.juego');
        Route::post('/puntuaciones/juego/{juego}', [PuntuacionController::class, 'publishPuntuacion'])->name('puntuaciones.publish');

        // Resultados personales y recepción de logros
        Route::get('/resultados/juego/{juego}', [ResultadoController::class, 'getResultadosByAuthUserJuegoId'])->name('resultados.juego');
        Route::post('/resultados/logro/{logro}', [ResultadoController::class, 'publishResultado'])->name('resultados.publish');

        // Amigos (Social)
        Route::get('/amigos', [AmigoController::class, 'getFriendsByAuthUser'])->name('amigos.auth');
        Route::get('/amigos/solicitudes', [AmigoController::class, 'getPendingRequests'])->name('amigos.solicitudes');
        Route::post('/amigos/solicitud', [AmigoController::class, 'requestFriendshipByAuthUser'])->name('amigos.request');
        Route::put('/amigos/{amigo}/aceptar', [AmigoController::class, 'acceptFriendship'])->name('amigos.accept');
        Route::delete('/amigos/{amigo}/rechazar', [AmigoController::class, 'rejectFriendship'])->name('amigos.reject');
        Route::delete('/amigos/{amigo}', [AmigoController::class, 'deleteFriendship'])->name('amigos.delete');

        /*
        |--------------------------------------------------------------------------
        | Admin Routes (Solo Administradores)
        |--------------------------------------------------------------------------
        */
        Route::middleware('rol:admin')->group(function () {
            // Dashboard
            Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

            // Usuarios
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
            Route::match(['put', 'patch'], '/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

            // Juegos (Gestión)
            Route::post('/juegos', [JuegoController::class, 'store'])->name('juegos.store');
            Route::match(['put', 'patch'], '/juegos/{juego}', [JuegoController::class, 'update'])->name('juegos.update');
            Route::delete('/juegos/{juego}', [JuegoController::class, 'destroy'])->name('juegos.destroy');

            // Logros (Gestión)
            Route::post('/logros', [LogroController::class, 'store'])->name('logros.store');
            Route::match(['put', 'patch'], '/logros/{logro}', [LogroController::class, 'update'])->name('logros.update');
            Route::delete('/logros/{logro}', [LogroController::class, 'destroy'])->name('logros.destroy');

            // Amigos (Gestión administrativa) — prefijo /admin/amigos para evitar conflicto con rutas de usuario
            Route::get('/admin/amigos', [AmigoController::class, 'index'])->name('amigos.index');
            Route::post('/admin/amigos', [AmigoController::class, 'store'])->name('amigos.store');
            Route::get('/admin/amigos/{amigo}', [AmigoController::class, 'show'])->name('amigos.show');
            Route::match(['put', 'patch'], '/admin/amigos/{amigo}', [AmigoController::class, 'update'])->name('amigos.update');
            Route::delete('/admin/amigos/{amigo}', [AmigoController::class, 'destroy'])->name('amigos.destroy');

            // Puntuaciones (Gestión administrativa)
            Route::get('/puntuaciones/user/{user?}', [PuntuacionController::class, 'getPuntuacionesByUserId'])->name('puntuaciones.user');
            Route::get('/puntuaciones/{puntuacion}', [PuntuacionController::class, 'show'])->name('puntuaciones.show');
            Route::post('/puntuaciones', [PuntuacionController::class, 'store'])->name('puntuaciones.store');
            Route::match(['put', 'patch'], '/puntuaciones/{puntuacion}', [PuntuacionController::class, 'update'])->name('puntuaciones.update');
            Route::delete('/puntuaciones/{puntuacion}', [PuntuacionController::class, 'destroy'])->name('puntuaciones.destroy');

            // Resultados (Gestión administrativa)
            Route::get('/resultados', [ResultadoController::class, 'index'])->name('resultados.index');
            Route::get('/resultados/user/{user?}', [ResultadoController::class, 'getResultadosByUserId'])->name('resultados.user');
            Route::get('/resultados/{resultado}', [ResultadoController::class, 'show'])->name('resultados.show');
            Route::post('/resultados', [ResultadoController::class, 'store'])->name('resultados.store');
            Route::match(['put', 'patch'], '/resultados/{resultado}', [ResultadoController::class, 'update'])->name('resultados.update');
            Route::delete('/resultados/{resultado}', [ResultadoController::class, 'destroy'])->name('resultados.destroy');
        });
    });
});
