<?php

namespace App\Models;

use Database\Factories\LogroFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logro extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return LogroFactory::new();
    }

    protected $table = 'logros';

    protected $fillable = [
        'juego_id',
        'name',
        'description'
    ];

    public function juego() {
        return $this->belongsTo(Juego::class, 'juego_id');
    }

    public function resultados() {
        return $this->hasMany(Resultado::class, 'logro_id');
    }

    public function rareza() {
        $juego = $this->juego();
        $puntuacionesJuego = $juego->puntuaciones(); // No detecta puntuaciones
        $users = $puntuacionesJuego->unique('user_id');
        $numberUsers = $users->count();
        $numberResultados = $this->resultados()->count();
        return ($numberResultados/$numberUsers)*100;
    }
}
