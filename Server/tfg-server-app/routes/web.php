<?php

use App\Http\Controllers\AmigoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JuegoController;
use App\Http\Controllers\LogroController;
use App\Http\Controllers\PuntuacionController;
use App\Http\Controllers\ResultadoController;
use App\Http\Controllers\UserController;
use App\Models\Puntuacion;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return redirect(route('dashboard.index'));
});

Route::get('/login', [AuthController::class, 'form'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth')->group(function () {

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::resource('juegos', JuegoController::class);
    Route::resource('logros', LogroController::class);
    Route::resource('resultados', ResultadoController::class);
    Route::resource('amigos', AmigoController::class);
    Route::resource('puntuaciones', PuntuacionController::class)->parameters(['puntuaciones' => 'puntuacion']);

    Route::middleware('rol:admin')->group(function () {
        Route::resource('users', UserController::class);
    });

});
