<?php

namespace App\Models;

use Database\Factories\PuntuacionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puntuacion extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return PuntuacionFactory::new();
    }

    protected $table = 'puntuaciones';

    protected $fillable = [
        'user_id',
        'juego_id',
        'puntuacion'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function juego() {
        return $this->belongsTo(Juego::class, 'juego_id');
    }
}
