<?php

namespace App\Providers;

use App\Models\Juego;
use App\Models\Logro;
use App\Policies\JuegoPolicy;
use App\Policies\LogroPolicy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Logro::class => LogroPolicy::class,
        Juego::class => JuegoPolicy::class,
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
