<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAmigoRequest;
use App\Http\Requests\UpdateAmigoRequest;
use App\Services\AmigoService;
use App\Helpers\JsonResponseBuilderHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

class AmigoController extends Controller
{
    public function __construct(
        private readonly AmigoService $amigoService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $amigos = $user->isAdmin() ? $this->amigoService->getAllAmigos() : $this->amigoService->getAmigosByUserId($user->id);
            return JsonResponseBuilderHelper::buildJsonSuccess('Amigos recibidos correctamente', ['data' => $amigos]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al recibir amigos: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAmigoRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $amigo = $this->amigoService->createAmigo($validated);
            return JsonResponseBuilderHelper::buildJsonSuccess('Amigo creado con exito', ['data' => $amigo]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al crear amigo: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $amigo = $this->amigoService->getAmigoById($id);
            if (!$amigo) {
                throw new Exception('Amigo no encontrado', 404);
            }
            return JsonResponseBuilderHelper::buildJsonSuccess('Amigo recibido correctamente', ['data' => $amigo]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al recibir amigo: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAmigoRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $updated = $this->amigoService->updateAmigo($id, $validated);
            if (!$updated) {
                throw new Exception('Amigo no encontrado o error al actualizar', 404);
            }
            return JsonResponseBuilderHelper::buildJsonSuccess('Amigo actualizado con exito', ['data' => $updated]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al actualizar amigo: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->amigoService->deleteAmigo($id);
            if (!$deleted) {
                throw new Exception('Amigo no encontrado o error al eliminar', 404);
            }
            return JsonResponseBuilderHelper::buildJsonSuccess('Amigo eliminado correctamente');
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al eliminar amigo: ' . $e->getMessage(), [], $e->getCode());
        }
    }
}
