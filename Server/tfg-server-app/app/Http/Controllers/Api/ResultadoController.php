<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ResultadoService;
use App\Helpers\JsonResponseBuilderHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class ResultadoController extends Controller
{
    public function __construct(
        private readonly ResultadoService $resultadoService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $resultados = $this->resultadoService->getAllResultados();
            return JsonResponseBuilderHelper::buildJsonSuccess('Resultados obtenidos con exito', ['data' => $resultados]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al obtener resultados: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $resultado = $this->resultadoService->createResultado($request->all());
            return JsonResponseBuilderHelper::buildJsonSuccess('Resultado creado con exito', ['data' => $resultado]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al crear resultado: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $resultado = $this->resultadoService->getResultadoById($id);
            if (!$resultado) {
                throw new Exception('Resultado no encontrado', 404);
            }
            return JsonResponseBuilderHelper::buildJsonSuccess('Resultado obtenido con exito', ['data' => $resultado]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al obtener resultado: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $updated = $this->resultadoService->updateResultado($id, $request->all());
            if (!$updated) {
                throw new Exception('Resultado no encontrado o error al actualizar', 404);
            }
            return JsonResponseBuilderHelper::buildJsonSuccess('Resultado actualizado con exito', ['data' => [$updated]]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al actualizar resultado: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->resultadoService->deleteResultado($id);
            if (!$deleted) {
                throw new Exception('Resultado no encontrado o error al eliminar', 404);
            }
            return JsonResponseBuilderHelper::buildJsonSuccess('Resultado eliminado con exito');
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al eliminar resultado: ' . $e->getMessage(), [], $e->getCode());
        }
    }
}
