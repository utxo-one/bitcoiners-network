<?php

namespace App\Services;

use App\Enums\ClassificationSource;
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
    public function classifyUser(?string $twitterId = null, ?TwitterUser $twitterUser = null, ?User $user = null): UserType
    {
        if (!$twitterId && !$twitterUser && !$user) {
            throw new Exception('Either twitterId, user or twitterUser must be provided');
        }

        // if the classification source is vote, do not reclassify and return the current type
        if ($user && $user->classification_source === ClassificationSource::VOTE) {
            return $user->type;
        }

        if (!$twitterUser && !$user) {
            $twitterUserClient = new UserClient(bearerToken: config('services.twitter.bearer_token'));
            $twitterUser = $twitterUserClient->getUserById($twitterId);
        }

        $bio = ($user->exists()) ? $user->twitter_description : $twitterUser->getDescription();
        $username = ($user->exists()) ? $user->name : $twitterUser->getName();

        $bioWords = collect(explode(' ', $bio));

        $bitcoinerKeywords = collect(config('classifier.bitcoinerKeywords'));
        $shitcoinerKeywords = collect(config('classifier.shitcoinerKeywords'));

        $bioContainsShitcoinKeywords = $bioWords
            ->contains(function ($word) use ($shitcoinerKeywords) {
                return $shitcoinerKeywords->contains($word);
            });

        $bioContainsBitcoinKeywords = $bioWords
            ->contains(function ($word) use ($bitcoinerKeywords) {
                return $bitcoinerKeywords->contains($word);
            });

        $nameContainsShitcoins = collect(config('classifier.shitcoinerNames'))
            ->contains(function ($shitcoinName) use ($username) {
                return str_contains($username, $shitcoinName);
         });

        $nameContainsBitcoin = collect(config('classifier.bitcoinerNames'))
            ->contains(function ($bitcoinName) use ($username) {
                return str_contains($username, $bitcoinName);
         });

        $type = UserType::NOCOINER;

        if ($bioContainsShitcoinKeywords || $nameContainsShitcoins) {
            $type = UserType::SHITCOINER;
        }

        if ($bioContainsBitcoinKeywords && !$bioContainsShitcoinKeywords && !$nameContainsShitcoins) {
            $type = UserType::BITCOINER;
        }

        if ($nameContainsBitcoin && !$bioContainsShitcoinKeywords && !$nameContainsShitcoins) {
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
            'classified_by' => ClassificationSource::CRAWLER,
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

    public function followUser(User $user): array
    {
        $userClient = new UserClient(
            apiKey: config('services.twitter.client_id'),
            apiSecret: config('services.twitter.client_secret'),
            accessToken: auth()->user()->oauth_token,
            accessSecret: auth()->user()->oauth_token_secret,
        );

        return $userClient->follow(
            authUserId: auth()->user()->twitter_id,
            userId: $user->twitter_id,
        )->getData();
    }

    public function isFollowing(User $user, User $targetUser): bool
    {
        return (Follow::where('follower_id', $user->twitter_id)->where('followee_id', $targetUser->twitter_id)->first() !== null);
    }

    public function isFollower(User $user, User $targetUser): bool
    {
        return (Follow::where('follower_id', $targetUser->twitter_id)->where('followee_id', $user->twitter_id)->first() !== null);
    }
}
