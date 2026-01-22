<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logro extends Model
{
    protected $table = 'logros';

    protected $fillable = [
        'juego',
        'name',
        'description'
    ];

    public function juego() {
        return $this->belongsTo(Juego::class, 'juego_id');
    }

    public function resultados() {
        return $this->hasMany(Resultado::class, 'logro_id');
    }
}
