<?php

namespace App\Http\Controllers\Frontend\User;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RefreshUserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    /**
     * Refresh User Details
     * 
     * Refresh twitter user details by username.
     * 
     * @group User
     * 
     * @queryParam username string required The username of the user to refresh details for. Example: @utxo_one
     *
     * @return JsonResponse
     */
    public function store(string $username): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        $this->userService->refreshUser($user);

        return response()->json($user, Response::HTTP_OK);
    }
}