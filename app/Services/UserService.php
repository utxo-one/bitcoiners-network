<?php

namespace App\Services;

use App\Enums\UserType;
use App\Models\Follow;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use UtxoOne\TwitterUltimatePhp\Clients\UserClient;
use UtxoOne\TwitterUltimatePhp\Models\User as TwitterUser;
use UtxoOne\TwitterUltimatePhp\Models\Users as TwitterUsers;

class UserService
{
    public function classifyUser(?string $twitterId = null, ?TwitterUser $twitterUser = null): UserType
    {
        if (!$twitterId && !$twitterUser) {
            throw new Exception('Either twitterId or twitterUser must be provided');
        }

        $twitterUserClient = new UserClient(bearerToken: config('services.twitter.bearer_token'));

        if (!$twitterUser) {
            $twitterUser = $twitterUserClient->getUserById($twitterId);
        }

        $twitterBioWordCollection = collect(explode(' ', $twitterUser->getDescription()));

        $bitcoinerKeywords = collect(config('classifier.bitcoinerKeywords'));
        $shitcoinerKeywords = collect(config('classifier.shitcoinerKeywords'));

        $bioContainsShitcoinKeywords = $twitterBioWordCollection->contains(function ($word) use ($shitcoinerKeywords) {
            return $shitcoinerKeywords->contains($word);
        });

        $bioContainsBitcoinKeywords = $twitterBioWordCollection->contains(function ($word) use ($bitcoinerKeywords) {
            return $bitcoinerKeywords->contains($word);
        });

        $nameContainsShitcoins = collect(config('classifier.shitcoinerNames'))->contains(function ($shitcoinName) use ($twitterUser) {
            return str_contains($twitterUser->getName(), $shitcoinName);
        });

        $nameContainsBitcoin = collect(config('classifier.bitcoinerNames'))->contains(function ($bitcoinName) use ($twitterUser) {
            return str_contains($twitterUser->getName(), $bitcoinName);
        });

        $type = UserType::NOCOINER;

        if ($bioContainsShitcoinKeywords || $nameContainsShitcoins) {
            $type = UserType::SHITCOINER;
        }

        if ($bioContainsBitcoinKeywords && !$bioContainsShitcoinKeywords) {
            $type = UserType::BITCOINER;
        }

        return $type;
    }

    public function saveTwitterUser(TwitterUser $twitterUser): User
    {
        $user = User::query()->firstOrNew(['twitter_id' => $twitterUser->getId()]);

        if ($user->exists) {
            return $user;
        }

        return User::create([
            'twitter_id' => $twitterUser->getId(),
            'name' => $twitterUser->getName(),
            'type' => $this->classifyUser(twitterUser: $twitterUser),
            'twitter_username' => $twitterUser->getUsername(),
            'twitter_description' => $twitterUser->getDescription(),
            'twitter_profile_image_url' => $twitterUser->getProfileImageUrl(),
            'twitter_url' => $twitterUser->getUrl(),
            'twitter_location' => $twitterUser->getLocation(),
            'twitter_verified' => $twitterUser->isVerified(),
            'twitter_pinned_tweet_id' => $twitterUser->getPinnedTweetId(),
            'twitter_count_followers' => $twitterUser->getPublicMetrics()->getFollowersCount(),
            'twitter_count_following' => $twitterUser->getPublicMetrics()->getFollowingCount(),
            'twitter_count_tweets' => $twitterUser->getPublicMetrics()?->getTweetCount(),
            'twitter_count_listed' => $twitterUser->getPublicMetrics()?->getListedCount(),
            'oauth_type'=> 'twitter',
            'password' => encrypt(str()->random(10)),
            'last_processed_at' => Carbon::now(),
        ]);
    }

    public function saveTwitterUsers(array $twitterUsers): void
    {
        foreach ($twitterUsers as $twitterUser) {
            $this->saveTwitterUser($twitterUser);
        }
    }

    public function saveFollowing(TwitterUser $twitterUser, ?string $nextToken = null, ?array $twitterUsers = []): array
    {
        $userClient = new UserClient(bearerToken: config('services.twitter.bearer_token'));

        $following = $userClient->getFollowing(
            id: $twitterUser->getId(),
            maxResults: 1000,
            paginationToken: $nextToken,
        );

        foreach ($following->all() as $followee) {
            $twitterUsers[] = $followee;

            $followExists = Follow::query()
                ->where('followee_id', $followee->getId())
                ->where('follower_id', $twitterUser->getId())
                ->first();

            if (!$followExists) {
                Follow::create([
                    'follower_id' => $twitterUser->getId(),
                    'followee_id' => $followee->getId(),
                ]);
            }
        }

        if ($following->getPaginationToken() !== null) {
            $this->saveFollowing($twitterUser, $following->getPaginationToken(), $twitterUsers);
        }

        return $twitterUsers;
    }

    public function saveFollowers(TwitterUser $twitterUser, ?string $nextToken = null, ?array $twitterUsers = []): array
    {
        $userClient = new UserClient(bearerToken: config('services.twitter.bearer_token'));

        $followers = $userClient->getFollowers(
            id: $twitterUser->getId(),
            maxResults: 1000,
            paginationToken: $nextToken,
        );

        foreach ($followers->all() as $follower) {
            $twitterUsers[] = $follower;

            $followExists = Follow::query()
                ->where('followee_id', $twitterUser->getId())
                ->where('follower_id', $follower->getId())
                ->first();

            if (!$followExists) {
                Follow::create([
                    'follower_id' => $follower->getId(),
                    'followee_id' => $twitterUser->getId(),
                ]);
            }
        }

        if ($followers->getPaginationToken() !== null) {
            $this->saveFollowers($twitterUser, $followers->getPaginationToken(), $twitterUsers);
        }

        return $twitterUsers;
    }

    public function processTwitterUsers(TwitterUsers $twitterUsers): void
    {
        foreach ($twitterUsers->all() as $twitterUser) {
            $this->processTwitterUser($twitterUser);
        }
    }

    public function processTwitterUser(TwitterUser $twitterUser): User
    {
        $user = $this->saveTwitterUser($twitterUser);
        $followers = $this->saveFollowers($twitterUser);
        $following = $this->saveFollowing($twitterUser);

        $this->saveTwitterUsers($followers);
        $this->saveTwitterUsers($following);

        $user->last_processed_at = Carbon::now();
        $user->save();

        return $user;
    }
}
