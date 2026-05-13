<?php

namespace App\Http\Controllers\Api;

use App\DTOs\User\UserDto;
use App\Helpers\AmigoHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\RequestFriendship;
use App\Http\Requests\StoreAmigoRequest;
use App\Http\Requests\UpdateAmigoRequest;
use App\Models\User;
use App\Services\AmigoService;
use App\Helpers\JsonResponseBuilderHelper;
use App\Models\Amigo;
use App\DTOs\Amigo\AmigoDto;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

class AmigoController extends Controller
{
    public function __construct(
        private readonly AmigoService $amigoService,
        private readonly UserService $userService,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $amigos = $user->isAdmin() ? $this->amigoService->getAllAmigos() : $this->amigoService->getAmigosByUserId($user->id);
            return JsonResponseBuilderHelper::buildJsonSuccess('Amistades recibidas correctamente', ['data' => $amigos]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAmigoRequest $request): JsonResponse
    {
        try {
            $amigoDto = AmigoDto::fromArray($request->validated());
            $amigo = $this->amigoService->createAmigo($amigoDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Amistad creada con exito', ['data' => $amigo]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Amigo $amigo): JsonResponse
    {
        try {
            $amigoDto = AmigoDto::fromModel($amigo);
            return JsonResponseBuilderHelper::buildJsonSuccess('Amistad recibida correctamente', ['data' => $amigoDto]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAmigoRequest $request, Amigo $amigo): JsonResponse
    {
        try {
            $amigoDto = AmigoDto::fromArray($request->validated());
            $updated = $this->amigoService->updateAmigo($amigo, $amigoDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Amistad actualizada con exito', ['data' => $updated]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Amigo $amigo): JsonResponse
    {
        try {
            $deleted = $this->amigoService->deleteAmigo($amigo);
            return JsonResponseBuilderHelper::buildJsonSuccess('Amistad eliminada correctamente');
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    public function getFriendsByAuthUser(): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user == null) {
                throw new Exception('Usuario no autenticado', 401);
            }
            $userDto = new UserDto(id: $user->id);
            $friends = $this->amigoService->getAmigosByUserId($userDto->id);
            return JsonResponseBuilderHelper::buildJsonSuccess('Amistades obtenidas correctamente', ['data' => $friends]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    public function requestFriendshipByAuthUser(RequestFriendship $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $user = Auth::user();
            $userDto = UserDto::fromModel($user);
            $friendDto = $this->userService->getUserByUsername($validated['username']);
            if (!$friendDto) {
                throw new Exception('Usuario no encontrado', 404);
            }
            [$userA, $userB] = AmigoHelper::orderUsersPairForAmigo($userDto, $friendDto);
            $amigoDto = new AmigoDto(
                user_id: $userA->id,
                friend_id: $userB->id,
            );
            $amigo = $this->amigoService->createAmigo($amigoDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Solicitud de amistad enviada correctamente', ['data' => $amigo]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    public function acceptFriendship(Amigo $amigo): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$amigo->userIsFriend($user)) {
                throw new Exception('No tienes permiso para aceptar esta solicitud de amistad', 403);
            }
            $amigoDto = AmigoDto::fromModel($amigo);
            $amigoDto->is_friend = true;
            $amigo = $this->amigoService->updateAmigo($amigo, $amigoDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Amistad aceptada correctamente', ['data' => $amigo]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    public function deleteFriendship(Amigo $amigo): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$amigo->userIsFriend($user)) {
                throw new Exception('No tienes permiso para eliminar esta solicitud de amistad', 403);
            }
            $deleted = $this->amigoService->deleteAmigo($amigo);
            return JsonResponseBuilderHelper::buildJsonSuccess('Amistad eliminada correctamente');
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }
}
