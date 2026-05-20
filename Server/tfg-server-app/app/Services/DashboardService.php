<?php

namespace App\Services;

use App\DTOs\Puntuacion\PuntuacionDto;
use App\DTOs\User\UserDto;
use App\Repositories\DashboardRepository;

class DashboardService
{
    public function __construct(
        private readonly DashboardRepository $dashboardRepository
    ) {
    }

    /**
     * Devuelve todos los datos necesarios para el dashboard de administrador.
     */
    public function getStats(): array
    {
        return [
            'recent_puntuaciones' => PuntuacionDto::collection(
                $this->dashboardRepository->getRecentPuntuaciones(5)
            ),
            'recent_users' => UserDto::collection(
                $this->dashboardRepository->getRecentUsers(5)
            ),
            'today_partidas'      => $this->dashboardRepository->getTodayPuntuacionesCount(),
            'total_users'         => $this->dashboardRepository->getTotalUsers(),
            'total_puntuaciones'  => $this->dashboardRepository->getTotalPuntuaciones(),
        ];
    }
}
