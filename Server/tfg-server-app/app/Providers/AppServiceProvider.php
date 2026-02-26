<?php

namespace App\Providers;

use App\Models\Juego;
use App\Models\Logro;
use App\Models\User;
use App\Models\Amigo;
use App\Models\Puntuacion;
use App\Models\Resultado;
use App\Policies\JuegoPolicy;
use App\Policies\LogroPolicy;
use App\Policies\UserPolicy;
use App\Policies\AmigoPolicy;
use App\Policies\PuntuacionPolicy;
use App\Policies\ResultadoPolicy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Logro::class => LogroPolicy::class,
        Juego::class => JuegoPolicy::class,
        User::class => UserPolicy::class,
        Amigo::class => AmigoPolicy::class,
        Puntuacion::class => PuntuacionPolicy::class,
        Resultado::class => ResultadoPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
