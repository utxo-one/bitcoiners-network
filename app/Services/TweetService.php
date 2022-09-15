<?php

namespace App\Services;

use App\Models\Tweet;
use App\Models\User;
use UtxoOne\TwitterUltimatePhp\Clients\TweetClient;

class TweetService
{
    public function saveUserTweets(User $user, ?int $amount = 50)
    {
        // Pending get timeline
    }
}