<?php

namespace App\Http\Controllers\Api;

use App\DTOs\User\UserDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use App\Helpers\JsonResponseBuilderHelper;
use App\Models\User;
use Auth;
use Illuminate\Http\JsonResponse;
use Exception;
use Request;

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
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $userDto = UserDto::fromArray($request->validated());
            $user = $this->userService->createUser($userDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('User creado con exito', ['data' => $user]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): JsonResponse
    {
        try {
            $userDto = UserDto::fromModel($user);
            return JsonResponseBuilderHelper::buildJsonSuccess('User retrieved successfully', ['data' => $userDto]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $userDto = UserDto::fromArray($request->validated());
            $updated = $this->userService->updateUser($user, $userDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('User actualizado con exito', ['data' => $updated]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        try {
            $deleted = $this->userService->deleteUser($user);
            return JsonResponseBuilderHelper::buildJsonSuccess('User eliminado con exito');
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    public function getAccount(): JsonResponse
    {
        try {
            $user = Auth::user();
            $userDto = UserDto::fromModel($user);
            return JsonResponseBuilderHelper::buildJsonSuccess('Cuenta obtenida con exito', ['data' => $userDto]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    public function updateOwnProfile(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $userDto = UserDto::fromArray($request->validated());
            $updated = $this->userService->updateUser($user, $userDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Perfil actualizado con exito', ['data' => $updated]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    public function eliminateAccount(): JsonResponse
    {
        try {
            $user = Auth::user();
            $deleted = $this->userService->deleteUser($user);
            return JsonResponseBuilderHelper::buildJsonSuccess('Cuenta eliminada con exito');
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }
}
