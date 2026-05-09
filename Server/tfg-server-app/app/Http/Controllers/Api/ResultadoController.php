<?php

namespace App\Http\Controllers\Api;

use App\DTOs\User\UserDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResultadoRequest;
use App\Http\Requests\UpdateResultadoRequest;
use App\Models\User;
use App\Services\ResultadoService;
use App\Helpers\JsonResponseBuilderHelper;
use App\Models\Resultado;
use App\DTOs\Resultado\ResultadoDto;
use Auth;
use Illuminate\Http\JsonResponse;
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
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResultadoRequest $request): JsonResponse
    {
        try {
            $resultadoDto = ResultadoDto::fromArray($request->validated());
            $resultado = $this->resultadoService->createResultado($resultadoDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Resultado creado con exito', ['data' => $resultado]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
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
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResultadoRequest $request, int $id): JsonResponse
    {
        try {
            $resultado = Resultado::find($id);
            if (!$resultado) {
                throw new Exception('Resultado no encontrado', 404);
            }
            $resultadoDto = ResultadoDto::fromArray($request->validated());
            $updated = $this->resultadoService->updateResultado($resultado, $resultadoDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Resultado actualizado con exito', ['data' => $updated]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Obtener los resultados de un usuario.
     */
    public function getResultadosByUserId(?User $user = null): JsonResponse
    {
        try {
            $user ??= Auth::user();
            throw_if(!$user, fn() => new Exception('Usuario no autenticado', 401));
            $userDto = new UserDto(id: $user->id);
            $resultados = $this->resultadoService->getResultadosByUserId($userDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Resultados obtenidos con exito', ['data' => $resultados]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $resultado = Resultado::find($id);
            if (!$resultado) {
                throw new Exception('Resultado no encontrado', 404);
            }
            $deleted = $this->resultadoService->deleteResultado($resultado);
            return JsonResponseBuilderHelper::buildJsonSuccess('Resultado eliminado con exito');
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }
}
