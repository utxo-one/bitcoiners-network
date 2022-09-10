<?php

namespace App\Http\Controllers\API;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(UserType $userType): JsonResponse
    {
        $users = User::query();

        if ($userType) {
            $users->where('type', $userType);
        }

        $users->take(10);

        return response()->json($users->paginate(perPage: 50));
    }

    public function show(string $username): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        $output = [
            'data' => [
                'user_data' => $user,
                'following_data' => [
                    'bitcoiners' => $user->follows()->where('type', UserType::BITCOINER)->count(),
                    'shitcoiners' => $user->follows()->where('type', UserType::SHITCOINER)->count(),
                    'nocoiners' => $user->follows()->where('type', UserType::NOCOINER)->count(),
                    'total' => $user->follows()->count(),
                ],
                'follower_data' => [
                    'bitcoiners' => $user->followers()->where('type', UserType::BITCOINER)->count(),
                    'shitcoiners' => $user->followers()->where('type', UserType::SHITCOINER)->count(),
                    'nocoiners' => $user->followers()->where('type', UserType::NOCOINER)->count(),
                    'total' => $user->followers()->count(),
                ],
            ],
        ];
        return response()->json($output);
    }
}
