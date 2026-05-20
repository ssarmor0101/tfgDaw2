<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJuegoRequest;
use App\Http\Requests\UpdateJuegoRequest;
use App\Services\JuegoService;
use App\Helpers\JsonResponseBuilderHelper;
use App\Models\Juego;
use App\DTOs\Juego\JuegoDto;
use Illuminate\Http\JsonResponse;
use Exception;

class JuegoController extends Controller
{
    public function __construct(
        private readonly JuegoService $juegoService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $juegos = $this->juegoService->getAllJuegos();
            return JsonResponseBuilderHelper::buildJsonSuccess('Juegos recuperados correctamente', ['data' => $juegos]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Juegos más populares (ordenados por número de puntuaciones).
     */
    public function popular(): JsonResponse
    {
        try {
            $juegos = $this->juegoService->getPopularJuegos();
            return JsonResponseBuilderHelper::buildJsonSuccess('Juegos populares recuperados correctamente', ['data' => $juegos]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Juegos más recientes (ordenados por fecha de creación).
     */
    public function recent(): JsonResponse
    {
        try {
            $juegos = $this->juegoService->getRecentJuegos();
            return JsonResponseBuilderHelper::buildJsonSuccess('Juegos recientes recuperados correctamente', ['data' => $juegos]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Búsqueda paginada de juegos con filtro por nombre y orden configurable.
     */
    public function buscar(\Illuminate\Http\Request $request): JsonResponse
    {
        try {
            $query   = $request->query('q', '');
            $order   = $request->query('order', 'popular_desc');
            $page    = (int) $request->query('page', 1);
            $perPage = (int) $request->query('per_page', 12);

            $result = $this->juegoService->searchJuegos($query, $order, $page, $perPage);
            return JsonResponseBuilderHelper::buildJsonSuccess('Juegos recuperados correctamente', $result);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJuegoRequest $request): JsonResponse
    {
        try {
            $juegoDto = JuegoDto::fromArray($request->validated());
            $juego = $this->juegoService->createJuego($juegoDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Juego creado con exito', ['data' => $juego]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Juego $juego): JsonResponse
    {
        try {
            $juegoDto = JuegoDto::fromModel($juego);
            return JsonResponseBuilderHelper::buildJsonSuccess('Juego recuperado correctamente', ['data' => $juegoDto]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJuegoRequest $request, Juego $juego): JsonResponse
    {
        try {
            $juegoDto = JuegoDto::fromArray($request->validated());
            $updated = $this->juegoService->updateJuego($juego, $juegoDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Juego actualizado con exito', ['data' => $updated]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Juego $juego): JsonResponse
    {
        try {
            $deleted = $this->juegoService->deleteJuego($juego);
            return JsonResponseBuilderHelper::buildJsonSuccess('Juego eliminado con exito');
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }
}
