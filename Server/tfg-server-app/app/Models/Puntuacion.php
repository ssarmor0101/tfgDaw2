<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Puntuacion extends Model
{
    protected $table = 'puntuaciones';

    protected $fillable = [
        'user',
        'juego',
        'puntuacion'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
