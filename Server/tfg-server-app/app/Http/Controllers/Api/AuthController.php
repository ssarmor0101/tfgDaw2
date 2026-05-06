<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Helpers\JsonResponseBuilderHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    /**
     * Procesa el login del usuario.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = $request->only('email', 'password');
            $authDto = $this->authService->login($credentials);

            if (!$authDto) {
                throw new Exception('Credenciales incorrectas', 401);
            }

            return JsonResponseBuilderHelper::buildJsonSuccess('Inicio de sesion exitoso', ['data' => $authDto]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error durante el login: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Procesa el logout del usuario.
     */
    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();
            return JsonResponseBuilderHelper::buildJsonSuccess('Cierre de sesion exitoso', []);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error durante el cierre de sesion: ' . $e->getMessage(), [], $e->getCode());
        }
    }
}
