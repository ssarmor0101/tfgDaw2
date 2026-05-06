<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJuegoRequest;
use App\Services\JuegoService;
use App\Helpers\JsonResponseBuilderHelper;
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
            return JsonResponseBuilderHelper::buildJsonError('Error al recuperar juegos: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJuegoRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $juego = $this->juegoService->createJuego($validated);
            return JsonResponseBuilderHelper::buildJsonSuccess('Juego creado con exito', ['data' => $juego]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al crear juego: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $juego = $this->juegoService->getJuegoById($id);
            if (!$juego) {
                throw new Exception('Juego no encontrado', 404);
            }
            return JsonResponseBuilderHelper::buildJsonSuccess('Juego recuperado correctamente', ['data' => $juego]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al recuperar juego: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreJuegoRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $updated = $this->juegoService->updateJuego($id, $validated);
            if (!$updated) {
                throw new Exception('Juego no encontrado o error al actualizar', 404);
            }
            return JsonResponseBuilderHelper::buildJsonSuccess('Juego actualizado con exito', ['data' => $updated]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al actualizar juego: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->juegoService->deleteJuego($id);
            if (!$deleted) {
                throw new Exception('Juego no encontrado o error al eliminar', 404);
            }
            return JsonResponseBuilderHelper::buildJsonSuccess('Juego eliminado con exito', ['data' => $deleted]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al eliminar juego: ' . $e->getMessage(), [], $e->getCode());
        }
    }
}
