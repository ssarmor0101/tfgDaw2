<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resultado extends Model
{
    protected $table = 'resultados';

    protected $fillable = [
        'usuario',
        'logro'
    ];

    public function usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function logro() {
        return $this->belongsTo(Logro::class, 'logro_id');
    }
}
