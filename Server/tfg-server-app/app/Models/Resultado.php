<?php

namespace App\Models;

use Database\Factories\ResultadoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resultado extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return ResultadoFactory::new();
    }

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
