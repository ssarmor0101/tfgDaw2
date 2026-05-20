<?php

namespace App\Repositories;

use App\Models\Puntuacion;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    /**
     * Devuelve las N puntuaciones más recientes con usuario y juego cargados.
     */
    public function getRecentPuntuaciones(int $limit = 5): Collection
    {
        return Puntuacion::with(['user', 'juego'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Devuelve los N usuarios más recientes con su rol cargado.
     */
    public function getRecentUsers(int $limit = 5): Collection
    {
        return User::with('rol')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Devuelve el número de puntuaciones registradas hoy.
     */
    public function getTodayPuntuacionesCount(): int
    {
        return Puntuacion::whereDate('created_at', today())->count();
    }

    /**
     * Devuelve el total de usuarios registrados.
     */
    public function getTotalUsers(): int
    {
        return User::count();
    }

    /**
     * Devuelve el total de puntuaciones registradas.
     */
    public function getTotalPuntuaciones(): int
    {
        return Puntuacion::count();
    }
}
