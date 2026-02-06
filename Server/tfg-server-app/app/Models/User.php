<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\RolSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function rol() {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function puntuaciones() {
        return $this->hasMany(Puntuacion::class, 'user_id');
    }

    public function resultados() {
        return $this->hasMany(Resultado::class, 'user_id');
    }

    public function amigos() {
        return $this->hasMany(Amigo::class, 'friend_id');
    }

    public function isAdmin(): bool
    {
        return $this->role->slug === RolSlug::ADMIN;
    }
}
