<?php

namespace App\Repositories;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserRepository 
{
    public function getFollowData(User $user): array
    {
        $cacheTime = 60;

        if ($user->twitter_count_followers > 1000) {
            $cacheTime = 600;
        }

        if ($user->twitter_count_followers > 5000) {
            $cacheTime = 1800;
        }

        if ($user->twitter_count_followers > 10000) {
            $cacheTime = 86400;
        }

        if ($user->twitter_count_followers > 50000) {
            $cacheTime = 604800;
        }

        return Cache::remember("follow_data:{$user->twitter_id}", $cacheTime, function () use ($user) {
            return [
                'following_data' => $user->following_data,
                'follower_data' => $user->follower_data,
            ];
        });
    }
}