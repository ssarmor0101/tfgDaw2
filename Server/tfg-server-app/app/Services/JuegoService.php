<?php

namespace App\Services;

use App\Models\Juego;

class JuegoService
{
    public function storeJuego(array $datos) : Juego
    {
        return Juego::create($datos);
    }
}