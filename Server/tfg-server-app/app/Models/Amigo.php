<?php

namespace App\Models;

use Database\Factories\AmigoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amigo extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return AmigoFactory::new();
    }

    protected $table = 'amigos';

    protected $fillable = [
        'usuario',
        'amigo'
    ];
}
