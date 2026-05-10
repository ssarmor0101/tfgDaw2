<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePuntuacionRequest;
use App\Http\Requests\UpdatePuntuacionRequest;
use App\Models\Juego;
use App\Models\User;
use App\Services\PuntuacionService;
use App\Helpers\JsonResponseBuilderHelper;
use App\Models\Puntuacion;
use App\DTOs\Puntuacion\PuntuacionDto;
use App\DTOs\User\UserDto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
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
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Puntuacion $puntuacion): JsonResponse
    {
        try {
            $puntuacionDto = PuntuacionDto::fromModel($puntuacion);
            return JsonResponseBuilderHelper::buildJsonSuccess('Puntuacion obtenida con exito', ['data' => $puntuacionDto]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePuntuacionRequest $request, Puntuacion $puntuacion): JsonResponse
    {
        try {
            $puntuacionDto = PuntuacionDto::fromArray($request->validated());
            $updated = $this->puntuacionService->updatePuntuacion($puntuacion, $puntuacionDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Puntuacion actualizada con exito', ['data' => $updated]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Puntuacion $puntuacion): JsonResponse
    {
        try {
            $deleted = $this->puntuacionService->deletePuntuacion($puntuacion);
            return JsonResponseBuilderHelper::buildJsonSuccess('Puntuacion eliminada con exito');
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Obtener las puntuaciones de un usuario.
     */
    public function getPuntuacionesByUserId(?User $user = null): JsonResponse
    {
        try {
            $user ??= Auth::user();
            if (!$user) {
                throw new Exception('Usuario no autenticado', 401);
            }
            $userDto = new UserDto(id: $user->id);
            $puntuaciones = $this->puntuacionService->getPuntuacionesByUserId($userDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Puntuaciones obtenidas con exito', ['data' => $puntuaciones]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    public function publishPuntuacion(Juego $juego, Request $request): JsonResponse
    {
        try {
            $puntuacionDto = new PuntuacionDto(
                puntuacion: $request->puntuacion,
                juego_id: $juego->id,
                user_id: Auth::user()->id,
            );
            $puntuacion = $this->puntuacionService->createPuntuacion($puntuacionDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Puntuacion publicada con exito', ['data' => $puntuacion]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }
}
