<?php

namespace App\Http\Controllers\Api;

use App\DTOs\User\UserDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use App\Helpers\JsonResponseBuilderHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $users = $this->userService->getAllUsers();
            return JsonResponseBuilderHelper::buildJsonSuccess('Users retrieved successfully', ['data' => $users]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Failed to retrieve users: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser($request->all());
            return JsonResponseBuilderHelper::buildJsonSuccess('User creado con exito', ['data' => $user]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al crear user: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->userService->getUserById($id);
            if (!$user) {
                throw new Exception('User not found', 404);
            }
            return JsonResponseBuilderHelper::buildJsonSuccess('User retrieved successfully', ['data' => $user]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error retrieving user: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        try {
            $userDto = UserDto::fromArray($request->validated());
            $updated = $this->userService->updateUser($id, $userDto->toArray());
            if (!$updated) {
                throw new Exception('User not found or update failed', 404);
            }
            return JsonResponseBuilderHelper::buildJsonSuccess('User actualizado con exito', ['data' => $updated]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al actualizar user: ' . $e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->userService->deleteUser($id);
            if (!$deleted) {
                throw new Exception('User not found or deletion failed', 404);
            }
            return JsonResponseBuilderHelper::buildJsonSuccess('User eliminado con exito');
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError('Error al eliminar user: ' . $e->getMessage(), [], $e->getCode());
        }
    }
}
