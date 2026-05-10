<?php

namespace App\Http\Controllers\Api;

use App\DTOs\User\UserDto;
use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Helpers\JsonResponseBuilderHelper;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly UserService $userService
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
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Registra un nuevo usuario.
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $userDto = UserDto::fromArray($validated);

            $user = $this->userService->createUser($userDto);

            return JsonResponseBuilderHelper::buildJsonSuccess('Registro exitoso', ['data' => $user]);
        } catch (Exception $e) {
            $code = $e->getCode() ?: 400;
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $code);
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
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }
}
