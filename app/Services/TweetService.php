<?php

namespace App\Services;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Support\Collection;
use UtxoOne\TwitterUltimatePhp\Models\Tweets;

class TweetService
{
    public function saveUserTweets(User $user, ?int $amount = 50)
    {
        // Pending get timeline
    }

    public function saveTweets(Tweets $tweets, User $user): Collection
    {
        $tweetCollection = [];

        foreach ($tweets->all() as $tweet) {
            $tweetCollection[] = Tweet::create([
                'user_id' => $user->twitter_id,
                'id' => $tweet->getId(),
                'text' => $tweet->getText(),
                'created_at' => $tweet->getCreatedAt(),
                'conversation_id' => $tweet->getConversationId(),
                'in_reply_to_user_id' => $tweet->getInReplyToUserId(),
                'lang' => $tweet->getLang(),
                'source' => $tweet->getSource(),
                'is_withheld' => ($tweet->isWithheld() === null) ? false : $tweet->isWithheld(),
                'public_metrics' => json_encode($tweet->getPublicMetrics()),
                'entities' => json_encode($tweet->getEntities()),
                'referenced_tweets' => json_encode($tweet->getReferencedTweets()),
                'geo' => json_encode($tweet->getGeo()),
                'is_possible_sensitive' => $tweet->isPossiblySensitive(),
                'attachements' => json_encode($tweet->getAttachments()),
                'reply_settings' => $tweet->getReplySettings(),
            ]);
        }

        $latestTweetDate = $user->tweets()->latest()->first()->created_at;
        $user->last_tweeted_at = $latestTweetDate;
        $user->save();

        return collect($tweetCollection);
    }
}