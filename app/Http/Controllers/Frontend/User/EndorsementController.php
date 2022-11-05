<?php

namespace App\Http\Controllers\Frontend\User;

use App\Enums\EndorsementType;
use App\Http\Controllers\Controller;
use App\Http\Requests\DestroyEndorsementRequest;
use App\Http\Requests\StoreEndorsementRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class EndorsementController extends Controller
{
    public function __construct(
        private UserService $userService,
        private UserRepository $userRepository,)
    {
    }

    public function index(string $twitterId): JsonResponse
    {
        $user = User::where('twitter_username', $twitterId)->firstOrFail();
        try {
            return response()->json($this->userRepository->getEndorsementData($user), Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }   
    }

    public function store(StoreEndorsementRequest $request): JsonResponse
    {
        $endorsement = $this->userService->endorseUser(
            endorserId: auth()->user()->twitter_id, 
            endorseeId: $request->input('twitterId'),
            type: $request->input('type')
        );

        if ($endorsement === true) {
            return response()->json(['message' => 'Endorsement created successfully'], Response::HTTP_CREATED);
        } 

        return response()->json(['message' => 'Endorsement already exists'], Response::HTTP_CONFLICT);
    }

    public function destroy(DestroyEndorsementRequest $request): JsonResponse
    {
        $endorsement = $this->userService->unendorseUser(
            endorserId: auth()->user()->twitter_id, 
            endorseeId: $request->input('twitterId'),
            type: $request->input('type')
        );

        if ($endorsement === true) {
            return response()->json(['message' => 'Endorsement deleted successfully'], Response::HTTP_OK);
        } 

        return response()->json(['message' => 'Endorsement does not exist'], Response::HTTP_NOT_FOUND);
    }

    public function types(): JsonResponse
    {
        return response()->json(EndorsementType::array(), Response::HTTP_OK);
    }

    public function typeIndex(string $type): JsonResponse
    {
        try {
            return response()->json($this->userRepository->getUsersByEndorsementType(EndorsementType::fromValue($type)), Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }   
    }

    public function authUserEndorsementsByUser(string $username): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();
        try {
            return response()->json($this->userRepository->getEndorsementsByAuthUserForUser($user), Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }   
    }
}