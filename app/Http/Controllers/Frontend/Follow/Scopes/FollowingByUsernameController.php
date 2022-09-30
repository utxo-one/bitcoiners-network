<?php

namespace App\Http\Controllers\Frontend\Follow\Scopes;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowingByUsernameController extends Controller
{
    /**
     * Get All Follows
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

        $following = $user->getFollowingByType($userType);

        return response()->json([
            'following' => $following->paginate(perPage: 20),
        ]);
    }

    public function all(string $username): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        $following = $user->follows();

        return response()->json([
            'following' => $following->paginate(perPage: 20),
        ]);
    }
}
