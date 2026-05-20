<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Amigo\AmigoDto;
use App\DTOs\User\UserDto;
use App\Helpers\AmigoHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAmigoRequest;
use App\Http\Requests\UpdateAmigoRequest;
use App\Models\Amigo;
use App\Services\AmigoService;
use App\Services\UserService;
use App\Helpers\JsonResponseBuilderHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class AmigoController extends Controller
{
    public function __construct(
        private readonly AmigoService $amigoService,
        private readonly UserService  $userService,
    ) {
    }

    // ─── Admin CRUD ───────────────────────────────────────────────────────────

    public function index(): JsonResponse
    {
        try {
            $amigos = $this->amigoService->getAllAmigos();
            return JsonResponseBuilderHelper::buildJsonSuccess('Amistades obtenidas correctamente', ['data' => $amigos]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    public function store(StoreAmigoRequest $request): JsonResponse
    {
        try {
            $amigoDto = AmigoDto::fromArray($request->validated());
            $amigo    = $this->amigoService->createAmigo($amigoDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Amistad creada con exito', ['data' => $amigo]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    public function show(Amigo $amigo): JsonResponse
    {
        try {
            $amigoDto = AmigoDto::fromModel($amigo->load('user', 'friend'));
            return JsonResponseBuilderHelper::buildJsonSuccess('Amistad obtenida correctamente', ['data' => $amigoDto]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    public function update(UpdateAmigoRequest $request, Amigo $amigo): JsonResponse
    {
        try {
            $amigoDto = AmigoDto::fromArray($request->validated());
            $updated  = $this->amigoService->updateAmigo($amigo, $amigoDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Amistad actualizada con exito', ['data' => $updated]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    public function destroy(Amigo $amigo): JsonResponse
    {
        try {
            $this->amigoService->deleteAmigo($amigo);
            return JsonResponseBuilderHelper::buildJsonSuccess('Amistad eliminada correctamente');
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    // ─── User endpoints ───────────────────────────────────────────────────────

    /** GET /amigos — confirmed friends of the authenticated user. */
    public function getFriendsByAuthUser(): JsonResponse
    {
        try {
            $user    = Auth::user();
            $friends = $this->amigoService->getConfirmedFriendsByUserId($user->id);
            return JsonResponseBuilderHelper::buildJsonSuccess('Amistades obtenidas correctamente', ['data' => $friends]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /** GET /amigos/solicitudes — pending requests received by the authenticated user. */
    public function getPendingRequests(): JsonResponse
    {
        try {
            $user    = Auth::user();
            $pending = $this->amigoService->getPendingReceivedByUserId($user->id);
            return JsonResponseBuilderHelper::buildJsonSuccess('Solicitudes pendientes obtenidas correctamente', ['data' => $pending]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /** POST /amigos/solicitud — send a friend request by username. */
    public function requestFriendshipByAuthUser(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate(['username' => 'required|string|max:255']);

            $user    = Auth::user();
            $userDto = UserDto::fromModel($user);

            // Find target user
            $friendDto = $this->userService->getUserByUsername($validated['username']);
            if (!$friendDto) {
                return JsonResponseBuilderHelper::buildJsonError('Usuario no encontrado', 404);
            }

            // Prevent self-request
            if ($friendDto->id === $user->id) {
                return JsonResponseBuilderHelper::buildJsonError('No puedes enviarte una solicitud a ti mismo', 422);
            }

            // Check existing relationship
            $existing = $this->amigoService->findByUsers($userDto, $friendDto);
            if ($existing) {
                $msg = $existing->receiver_id !== null
                    ? 'Ya existe una solicitud de amistad pendiente entre vosotros'
                    : 'Ya sois amigos';
                return JsonResponseBuilderHelper::buildJsonError($msg, 409);
            }

            [$userA, $userB] = AmigoHelper::orderUsersPairForAmigo($userDto, $friendDto);
            $amigoDto = new AmigoDto(
                user_id:     $userA->id,
                friend_id:   $userB->id,
                receiver_id: $friendDto->id,
            );
            $amigo = $this->amigoService->createAmigo($amigoDto);
            return JsonResponseBuilderHelper::buildJsonSuccess('Solicitud de amistad enviada correctamente', ['data' => $amigo]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => ['success' => false, 'message' => collect($e->errors())->flatten()->first() ?? 'Error de validación'],
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /** PUT /amigos/{amigo}/aceptar — only the receiver can accept. */
    public function acceptFriendship(Amigo $amigo): JsonResponse
    {
        try {
            $user = Auth::user();

            if ($amigo->receiver_id === null) {
                return JsonResponseBuilderHelper::buildJsonError('Esta solicitud ya fue aceptada', 409);
            }
            if ($amigo->receiver_id !== $user->id) {
                return JsonResponseBuilderHelper::buildJsonError('Solo el receptor puede aceptar esta solicitud', 403);
            }

            $amigoDto = $this->amigoService->acceptAmigo($amigo);
            return JsonResponseBuilderHelper::buildJsonSuccess('Amistad aceptada correctamente', ['data' => $amigoDto]);
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /** DELETE /amigos/{amigo}/rechazar — only the receiver can reject a pending request. */
    public function rejectFriendship(Amigo $amigo): JsonResponse
    {
        try {
            $user = Auth::user();

            if ($amigo->receiver_id === null) {
                return JsonResponseBuilderHelper::buildJsonError('Esta amistad ya está confirmada', 409);
            }
            if ($amigo->receiver_id !== $user->id) {
                return JsonResponseBuilderHelper::buildJsonError('Solo el receptor puede rechazar esta solicitud', 403);
            }

            $this->amigoService->deleteAmigo($amigo);
            return JsonResponseBuilderHelper::buildJsonSuccess('Solicitud rechazada correctamente');
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }

    /** DELETE /amigos/{amigo} — either party can remove a confirmed friendship or cancel a sent request. */
    public function deleteFriendship(Amigo $amigo): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$amigo->userIsFriend($user)) {
                return JsonResponseBuilderHelper::buildJsonError('No tienes permiso para eliminar esta amistad', 403);
            }

            $this->amigoService->deleteAmigo($amigo);
            return JsonResponseBuilderHelper::buildJsonSuccess('Amistad eliminada correctamente');
        } catch (Exception $e) {
            return JsonResponseBuilderHelper::buildJsonError($e->getMessage(), $e->getCode());
        }
    }
}
