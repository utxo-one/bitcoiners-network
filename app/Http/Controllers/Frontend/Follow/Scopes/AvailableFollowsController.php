<?php

namespace App\Http\Controllers\Frontend\Follow\Scopes;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailableFollowsController extends Controller
{
    /**
     * Get All Available Follows
     * 
     * A list of users, by user type, that the authenticated user does not already follow.
     * 
     * @group Follow
     *
     * @param UserType $userType
     * @return JsonResponse
     */
    public function index(UserType $userType): JsonResponse
    {
        $availableFollows = User::where('type', UserType::BITCOINER);

        return response()->json([
            'availableFollows' => $availableFollows->paginate(perPage: 20),
        ]);
    }
}
