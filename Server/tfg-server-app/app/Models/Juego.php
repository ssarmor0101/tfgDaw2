<?php

namespace App\Models;

use Database\Factories\JuegoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Juego extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected static function newFactory()
    {
        return JuegoFactory::new();
    }

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
