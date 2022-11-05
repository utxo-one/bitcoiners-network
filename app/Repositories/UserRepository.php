<?php

namespace App\Repositories;

use App\Enums\EndorsementType;
use App\Enums\UserType;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserRepository 
{
    private int $cacheTime;

    public function __construct()
    {
        $this->cacheTime = 1000;
    }

    public function getFollowData(User $user): array
    {
        $this->cacheTime = 1000;

        if ($user->twitter_count_followers > 1000) {
            $this->cacheTime = 6000;
        }

        if ($user->twitter_count_followers > 5000) {
            $this->cacheTime = 18000;
        }

        if ($user->twitter_count_followers > 10000) {
            $this->cacheTime = 286400;
        }

        if ($user->twitter_count_followers > 50000) {
            $this->cacheTime = 604800;
        }

        return Cache::remember("follow_data:{$user->twitter_id}", $this->cacheTime, function () use ($user) {
            return [
                'following_data' => $user->following_data,
                'follower_data' => $user->follower_data,
            ];
        });
    }

    public function getEndorsementData(User $user): array
    {
        return Cache::remember("endorsement_data:{$user->twitter_id}", $this->cacheTime, function () use ($user) {

            // For each endorsement type, get the count of endorsements
            $endorsementData = [];

            foreach (EndorsementType::values() as $endorsementType) {
                $endorsementData[$endorsementType] = $user->endorsementsReceived()->where('endorsement_type', $endorsementType)->count();
            }
            return [
                'endorsement_data' => $endorsementData,
            ];
        });
    }

    public function getUsersByEndorsementType(EndorsementType $endorsementType): array
    {
        // Get the users who have received the endorsement type.
        $users = User::whereHas('endorsementsReceived', function ($query) use ($endorsementType) {
            $query->where('endorsement_type', $endorsementType);
        })->get();

        // For each user, get the count of endorsements.
        $usersWithEndorsementCount = [];

        foreach ($users as $user) {
            $usersWithEndorsementCount[] = [
                'user' => $user,
                'endorsement_count' => $user->endorsementsReceived()->where('endorsement_type', $endorsementType)->count(),
            ];
        }

        // Sort the users by endorsement count.
        usort($usersWithEndorsementCount, function ($a, $b) {
            return $b['endorsement_count'] <=> $a['endorsement_count'];
        });

        return Cache::remember("users_by_endorsement_type:{$endorsementType->value}", $this->cacheTime, function () use ($usersWithEndorsementCount) {
            return $usersWithEndorsementCount;
        });
    }

    public function getEndorsementsByAuthUserForUser(User $user): array
    {
        return $user->endorsements()->where('user_id', auth()->user()->id)->get();
    }
}