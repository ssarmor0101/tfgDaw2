<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Juego extends Model
{
    protected $table = 'juegos';

    protected $fillable = [
        'name',
        'description'
    ];

    public function logros() {
        return $this->hasMany(Logro::class, 'juego_id');
    }

    public function puntuaciones() {
        return $this->hasMany(Puntuacion::class, 'juego_id');
    }
}
