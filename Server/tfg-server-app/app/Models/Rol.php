<?php

namespace App\Models;

use App\Enums\RolSlug;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $timestamp = false;

    protected $table = 'roles';

    protected $fillable = [
        'name'
    ];

    protected $casts = [
        'slug' => RolSlug::class
    ];
}
