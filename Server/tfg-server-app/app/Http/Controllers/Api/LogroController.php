<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLogroRequest;
use App\Services\LogroService;
use App\Helpers\JsonResponseBuilderHelper;
use Illuminate\Http\JsonResponse;
use Exception;

class LogroController extends Controller
{
    public function __construct(
        private readonly LogroService $logroService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $logros = $this->logroService->getAllLogros();
            return JsonResponseBuilderHelper::buildJsonSuccess('Logros obtenidos con exito', ['data' => $logros]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al obtener logros: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLogroRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $logro = $this->logroService->createLogro($validated);
            return JsonResponseBuilderHelper::buildJsonSuccess('Logro creado con exito', ['data' => $logro]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al crear logro: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $logro = $this->logroService->getLogroById($id);
            if (!$logro) {
                throw new Exception('Logro no encontrado', 404);
            }
            return JsonResponseBuilderHelper::buildJsonSuccess('Logro obtenido con exito', ['data' => $logro]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al obtener logro: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreLogroRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $updated = $this->logroService->updateLogro($id, $validated);
            if (!$updated) {
                throw new Exception('Logro no encontrado o error al actualizar', 404);
            }
            return JsonResponseBuilderHelper::buildJsonSuccess('Logro actualizado con exito');
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al actualizar logro: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->logroService->deleteLogro($id);
            if (!$deleted) {
                throw new Exception('Logro no encontrado o error al eliminar', 404);
            }
            return JsonResponseBuilderHelper::buildJsonSuccess('Logro eliminado con exito');
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al eliminar logro: ' . $e->getMessage(), [], $e->getCode());
        }
    }
}
