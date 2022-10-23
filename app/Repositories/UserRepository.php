<?php

namespace App\Repositories;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserRepository 
{
    public function getFollowData(User $user): array
    {
        $cacheTime = 1000;

        if ($user->twitter_count_followers > 1000) {
            $cacheTime = 6000;
        }

        if ($user->twitter_count_followers > 5000) {
            $cacheTime = 18000;
        }

        if ($user->twitter_count_followers > 10000) {
            $cacheTime = 286400;
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