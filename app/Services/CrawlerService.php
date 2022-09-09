<?php

namespace App\Services;

use App\Enums\UserType;
use App\Models\User;
use UtxoOne\TwitterUltimatePhp\Clients\UserClient;
use UtxoOne\TwitterUltimatePhp\Models\Users as TwitterUsers;

class CrawlerService
{
    public function processTwitterUsers(TwitterUsers $twitterUsers): void
    {
        foreach($twitterUsers->all() as $twitterUser) {

            // Check if the user already exists in the database and skip iteration if so.
            $userExists = User::where('twitter_id', $twitterUser->getId())->first();
            if ($userExists) {
                continue;
            }

            // Classify the user.
            $userService = new UserService();
            $userType = $userService->classifyUser(twitterUser: $twitterUser);

            // Save user to database.
            $userService->saveTwitterUser($twitterUser, $userType);
        }
    }

    public function crawlBitcoiners(): void
    {
        $userClient = new UserClient(bearerToken: config('services.twitter.bearer_token'));
        $bitcoiners = User::where('type', UserType::BITCOINER)->get();

        foreach($bitcoiners as $bitcoiner) {
            $followers = $userClient->getFollowers($bitcoiner->twitter_id);
            $this->processTwitterUsers($followers);
        }

        $this->crawlBitcoiners();
    }
}