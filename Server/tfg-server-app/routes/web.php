<?php

use App\Http\Controllers\JuegoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resources([
    'juegos' => JuegoController::class,
]);