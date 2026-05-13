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
