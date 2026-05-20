<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Helpers\JsonResponseBuilderHelper;
use Illuminate\Http\JsonResponse;
use Exception;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {
    }

    /**
     * Devuelve las estadísticas del panel de administración.
     */
    public function index(): JsonResponse
    {
        try {
            $stats = $this->dashboardService->getStats();
            return JsonResponseBuilderHelper::buildJsonSuccess(
                'Dashboard obtenido con exito',
                ['data' => $stats]
            );
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }
}
