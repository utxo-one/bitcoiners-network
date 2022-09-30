<?php

namespace App\Http\Controllers\Frontend\Follow\Scopes;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowerByUsernameController extends Controller
{
    /**
     * Get All Followers
     * 
     * A list of users that the authenticated user is following.
     * 
     * @group Follow
     *
     * @param UserType $userType
     * @return JsonResponse
     */
    public function index(string $username, UserType $userType): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        $followers = $user->getFollowersByType($userType);

        return response()->json([
            'followers' => $followers->paginate(perPage: 20),
        ]);
    }

    public function all(string $username): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        $followers = $user->followers();

        return response()->json([
            'followers' => $followers->paginate(perPage: 20),
        ]);
    }
}
