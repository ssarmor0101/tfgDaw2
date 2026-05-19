<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Auth\AuthDto;
use App\DTOs\User\UserDto;
use App\Enums\RolSlug;
use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\User;
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

            $userRole = Rol::where('slug', RolSlug::USER->value)->first();
            $userDto = UserDto::fromArray(array_merge($validated, ['rol_id' => $userRole?->id]));

            $createdUser = $this->userService->createUser($userDto);

            $userModel = User::with('rol')->find($createdUser->id);
            $token = $userModel->createToken('api-token')->plainTextToken;
            $authDto = new AuthDto(
                token: $token,
                user: UserDto::fromModel($userModel)->toArray()
            );

            return JsonResponseBuilderHelper::buildJsonSuccess('Registro exitoso', ['data' => $authDto]);
        } catch (Exception $e) {
            $code = is_int($e->getCode()) ? ($e->getCode() ?: 400) : 400;
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
