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
     * Get Bitcoiners by Bitcoiner Followers
     *
     * @return Collection
     */    
    public function getBitcoinersByBitcoinerFollowers(): Collection
    {
        return $this->getLeaderboard(
            minFollowers: 500,
            maxFollowers: 5000000,
            userType: UserType::BITCOINER,
            followType: FollowType::FOLLOWER,
            followUserType: UserType::BITCOINER,
        );
    }

    /**
     * Get Bitcoiners by Bitcoiners Following
     * 
     * @return Collection
     */
    public function getBitcoinersByBitcoinersFollowing(): Collection
    {
        return $this->getLeaderboard(
            minFollowers: 500,
            maxFollowers: 5000000,
            userType: UserType::BITCOINER,
            followType: FollowType::FOLLOWING,
            followUserType: UserType::BITCOINER,
        );
    }

    /**
     * Get Bitcoiners by Shitcoiner Followers
     * 
     * @return Collection
     */
    public function getBitcoinersByShitcoinerFollowers(): Collection
    {
        return $this->getLeaderboard(
            minFollowers: 500,
            maxFollowers: 5000000,
            userType: UserType::BITCOINER,
            followType: FollowType::FOLLOWER,
            followUserType: UserType::SHITCOINER,
        );
    }

    /**
     * Get Bitcoiners by Shitcoiners Following
     * 
     * @return Collection
     */
    public function getBitcoinersByShitcoinersFollowing(): Collection
    {
        return $this->getLeaderboard(
            minFollowers: 500,
            maxFollowers: 5000000,
            userType: UserType::BITCOINER,
            followType: FollowType::FOLLOWING,
            followUserType: UserType::SHITCOINER,
        );
    }

    /**
     * Get Bitcoiners by Nocoiner Followers
     * 
     * @return Collection
     */
    public function getBitcoinersByNocoinerFollowers(): Collection
    {
        return $this->getLeaderboard(
            minFollowers: 500,
            maxFollowers: 5000000,
            userType: UserType::BITCOINER,
            followType: FollowType::FOLLOWER,
            followUserType: UserType::NOCOINER,
        );
    }

    /**
     * Get Bitcoiners by Nocoiners Following
     * 
     * @return Collection
     */
    public function getBitcoinersByNocoinersFollowing(): Collection
    {
        return $this->getLeaderboard(
            minFollowers: 500,
            maxFollowers: 5000000,
            userType: UserType::BITCOINER,
            followType: FollowType::FOLLOWING,
            followUserType: UserType::NOCOINER,
        );
    }

    /**
     * Get Leaderboard
     * 
     * Get the collection from the cache if it exists, otherwise build the cache.
     * Follow data from user repository is also either retrieved from cache or built.
     *
     * @return Collection
     */
    private function getLeaderboard(
        int $minFollowers,
        int $maxFollowers,
        UserType $userType,
        FollowType $followType,
        UserType $followUserType,
    ): Collection {
        // Initialize a collection to store the results.
        $results = collect();

        // Create a cache key for the leaderboard.
        $cacheKey = 'leaderboard:' . $userType->value . '-by-' . $followUserType->value . '-' . $followType->value;

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

            // Select all users with over 1,000 followers.
            $users = User::query()
                ->where('twitter_count_followers', '>', $minFollowers)
                ->where('twitter_count_followers', '<', $maxFollowers)
                ->where('type', $userType)
                ->orderBy('twitter_count_' . $followTypeKey, 'desc')
                ->get();

            // For each user, get the follow data, user model and rank and add it to the collection.
            $users->each(function ($user) use ($results, $followType, $followUserType, $followTypeKey) {
                $followData = $this->userRepository->getFollowData($user);
                
                return $results->push([
                    'user' => $user,
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
                    $followUserType->value . 's_' . $followTypeKey => $result[$followUserType->value .'_'. $followTypeKey],
                ];
            });

            return $results;
        });
    }
}