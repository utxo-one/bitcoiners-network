<?php

namespace App\Repositories;

use App\Enums\FollowType;
use App\Enums\UserType;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class LeaderboardRepository 
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * Get Leaderboard
     * 
     * Get the collection from the cache if it exists, otherwise build the cache.
     * Follow data from user repository is also either retrieved from cache or built.
     *
     * @return Collection
     */
    public function getLeaderboard(
        int $minFollowers,
        int $maxFollowers,
        UserType $userType,
        FollowType $followType,
        UserType $followUserType,
    ): Collection {

        $results = collect();

        $cacheKey = 'leaderboard:' . $userType->value . '-by-' . $followUserType->value . '-' . $followType->value . '-between-' . $minFollowers . '-and-' . $maxFollowers;

        return Cache::remember($cacheKey, 86400, function () use (
            $results,
            $cacheKey,
            $minFollowers,
            $maxFollowers,
            $userType,
            $followType,
            $followUserType,
        ) {

            $followTypeKey = ($followType === FollowType::FOLLOWING) ? $followType->value : $followType->value . 's';

            $users = User::query()
                ->where('twitter_count_followers', '>', $minFollowers)
                ->where('twitter_count_followers', '<', $maxFollowers)
                ->where('type', $userType)
                ->orderBy('twitter_count_' . $followTypeKey, 'desc')
                ->get();

            // For each user add the follow data.
            $users->each(function ($user) use ($results, $followType, $followUserType, $followTypeKey) {
                $followData = $this->userRepository->getFollowData($user);
                
                return $results->push([
                    'user' => $user,
                    'followData' => $followData,
                    $followUserType->value . '_' . $followTypeKey => $followData[$followType->value . '_data'][str()->plural($followUserType->value)],
                ]);
            });

            // Sort the collection by the number of bitcoiner followers.
            $results = $results->sortByDesc($followUserType->value . '_'. $followTypeKey);
            $rank = 0;

            // Build a new collection, this time with the rank and the user model.
            $results = $results->map(function ($result) use (&$rank, $cacheKey, $followUserType, $followTypeKey) {

                // Cache the user rank.
                Cache::put($cacheKey . ':' . $result['user']->twitter_username, ++$rank, 86400);

                return [
                    'rank' => $rank,
                    'user' => $result['user'],
                    'followData' => $result['followData'],
                    $followUserType->value . 's_' . $followTypeKey => $result[$followUserType->value .'_'. $followTypeKey],
                ];
            });

            return $results;
        });
    }
}