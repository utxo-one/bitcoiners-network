<?php

namespace App\Services;

use App\Enums\UserType;
use App\Models\User;
use Exception;
use UtxoOne\TwitterUltimatePhp\Clients\UserClient;
use UtxoOne\TwitterUltimatePhp\Models\User as TwitterUser;

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

    public function saveTwitterUser(TwitterUser $twitterUser, UserType $userType): User
    {
        $userExists = User::where('twitter_id', $twitterUser->getId())->first();
  
        if ($userExists) {
            return $userExists;
        }

        return User::create([
             'name' => $twitterUser->getName(),
             'twitter_id'=> $twitterUser->getId(),
             'type' => $userType,
             'twitter_username' => $twitterUser->getUsername(),
             'twitter_description' => $twitterUser->getDescription(),
             'twitter_profile_image_url' => $twitterUser->getProfileImageUrl(),
             'twitter_url' => $twitterUser->getUrl(),
             'twitter_location' => $twitterUser->getLocation(),
             'twitter_verified' => $twitterUser->isVerified(),
             'twitter_followers_count' => $twitterUser->getPublicMetrics()?->getFollowersCount(),
             'twitter_following_count' => $twitterUser->getPublicMetrics()?->getFollowingCount(),
             'twitter_tweet_count' => $twitterUser->getPublicMetrics()?->getTweetCount(),
             'twitter_listed_count' => $twitterUser->getPublicMetrics()?->getListedCount(),
             'oauth_type'=> 'twitter',
             'password' => encrypt(str()->random(10))
         ]);
    }
}
