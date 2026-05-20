<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLogroRequest;
use App\Http\Requests\UpdateLogroRequest;
use App\Services\LogroService;
use App\Helpers\JsonResponseBuilderHelper;
use App\Models\Juego;
use App\Models\Logro;
use App\DTOs\Logro\LogroDto;
use Illuminate\Http\JsonResponse;
use Exception;

class LogroController extends Controller
{
    public function __construct(
        private readonly LogroService $logroService
    ) {
    }

    /**
     * Display logros filtered by game.
     */
    public function getLogrosByJuegoId(Juego $juego): JsonResponse
    {
        try {
            $logros = $this->logroService->getLogrosByJuegoId($juego->id);
            return JsonResponseBuilderHelper::buildJsonSuccess('Logros obtenidos con exito', ['data' => $logros]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
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
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLogroRequest $request): JsonResponse
    {
        try {
            $logroDto = LogroDto::fromArray($request->validated());
            $logro = $this->logroService->createLogro($logroDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Logro creado con exito', ['data' => $logro]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Logro $logro): JsonResponse
    {
        try {
            $logroDto = LogroDto::fromModel($logro);
            return JsonResponseBuilderHelper::buildJsonSuccess('Logro obtenido con exito', ['data' => $logroDto]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLogroRequest $request, Logro $logro): JsonResponse
    {
        try {
            $logroDto = LogroDto::fromArray($request->validated());
            $updated = $this->logroService->updateLogro($logro, $logroDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Logro actualizado con exito', ['data' => $updated]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Logro $logro): JsonResponse
    {
        try {
            $this->logroService->deleteLogro($logro);
            return JsonResponseBuilderHelper::buildJsonSuccess('Logro eliminado con exito');
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }
}
