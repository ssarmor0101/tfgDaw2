<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePuntuacionRequest;
use App\Http\Requests\UpdatePuntuacionRequest;
use App\Services\PuntuacionService;
use App\Helpers\JsonResponseBuilderHelper;
use App\Models\Puntuacion;
use App\DTOs\Puntuacion\PuntuacionDto;
use App\DTOs\User\UserDto;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

class PuntuacionController extends Controller
{
    public function __construct(
        private readonly PuntuacionService $puntuacionService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $puntuaciones = $this->puntuacionService->getAllPuntuaciones();
            return JsonResponseBuilderHelper::buildJsonSuccess('Puntuaciones obtenidas con exito', ['data' => $puntuaciones]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al obtener puntuaciones: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePuntuacionRequest $request): JsonResponse
    {
        try {
            $puntuacionDto = PuntuacionDto::fromArray($request->validated());
            $puntuacion = $this->puntuacionService->createPuntuacion($puntuacionDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Puntuacion creada con exito', ['data' => $puntuacion]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al crear puntuacion: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $puntuacion = $this->puntuacionService->getPuntuacionById($id);
            if (!$puntuacion) {
                throw new Exception('Puntuacion no encontrada', 404);
            }
            return JsonResponseBuilderHelper::buildJsonSuccess('Puntuacion obtenida con exito', ['data' => $puntuacion]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al obtener puntuacion: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePuntuacionRequest $request, int $id): JsonResponse
    {
        try {
            $puntuacion = Puntuacion::find($id);
            if (!$puntuacion) {
                throw new Exception('Puntuacion no encontrada', 404);
            }
            $puntuacionDto = PuntuacionDto::fromArray($request->validated());
            $updated = $this->puntuacionService->updatePuntuacion($puntuacion, $puntuacionDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Puntuacion actualizada con exito', ['data' => $updated]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al actualizar puntuacion: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Obtener las puntuaciones del usuario autenticado.
     */
    public function getMyPuntuaciones(): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new Exception('Usuario no autenticado', 401);
            }
            $userDto = new UserDto(id: $user->id);
            $puntuaciones = $this->puntuacionService->getPuntuacionesByUserId($userDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Puntuaciones obtenidas con exito', ['data' => $puntuaciones]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al obtener puntuaciones: ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $puntuacion = Puntuacion::find($id);
            if (!$puntuacion) {
                throw new Exception('Puntuacion no encontrada', 404);
            }
            $deleted = $this->puntuacionService->deletePuntuacion($puntuacion);
            return JsonResponseBuilderHelper::buildJsonSuccess('Puntuacion eliminada con exito');
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al eliminar puntuacion: ' . $e->getMessage(), $e->getCode());
        }
    }
}
