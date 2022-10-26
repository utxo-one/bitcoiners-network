<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Support\Collection;
use UtxoOne\TwitterUltimatePhp\Clients\TweetClient;
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

            // if the tweet already exists, skip it
            if (Tweet::query()->where('id', $tweet->getId())->exists()) {
                continue;
            }

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
                'replies' => $tweet->getPublicMetrics()['reply_count'],
                'retweets' => $tweet->getPublicMetrics()['retweet_count'],
                'likes' => $tweet->getPublicMetrics()['like_count'],
                'quotes' => $tweet->getPublicMetrics()['quote_count'],
            ]);
        }

        $latestTweetDate = $user->tweets()->latest()->first()->created_at;
        $user->last_tweeted_at = $latestTweetDate;
        $user->save();

        return collect($tweetCollection);
    }

    public function like(Tweet $tweet): array
    {
        $tweetClient = new TweetClient(
            apiKey: config('services.twitter.client_id'),
            apiSecret: config('services.twitter.client_secret'),
            accessToken: auth()->user()->oauth_token,
            accessSecret: auth()->user()->oauth_token_secret,
        );

        $like = $tweetClient->likeTweet(
            authUserId: auth()->user()->twitter_id,
            tweetId: $tweet->id
        );

        if (!$like) {
            throw new \Exception('Could not like tweet');
        }

        $like = auth()->user()->likes()->create([
            'target_id' => $tweet->id,
        ]);

        $transaction = Transaction::create([
            'user_id' => auth()->user()->twitter_id,
            'type' => TransactionType::DEBIT,
            'amount' => config('pricing.like'),
            'description' => 'Liked Tweet ' . $tweet->id,
            'status' => TransactionStatus::FINAL,
        ]);

        return [
            'like' => $like,
            'transaction' => $transaction,
        ];
    }

    public function unlike(Tweet $tweet): array
    {
        $tweetClient = new TweetClient(
            apiKey: config('services.twitter.client_id'),
            apiSecret: config('services.twitter.client_secret'),
            accessToken: auth()->user()->oauth_token,
            accessSecret: auth()->user()->oauth_token_secret,
        );

        $unlike = $tweetClient->unlikeTweet(
            authUserId: auth()->user()->twitter_id,
            tweetId: $tweet->id
        );

        if (!$unlike) {
            throw new \Exception('Could not unlike tweet');
        }

        $unlike = auth()->user()->likes()->where('target_id', $tweet->id)->delete();

        $transaction = Transaction::create([
            'user_id' => auth()->user()->twitter_id,
            'type' => TransactionType::DEBIT,
            'amount' => config('pricing.like'),
            'description' => 'Unliked Tweet ' . $tweet->id,
            'status' => TransactionStatus::FINAL,
        ]);

        return [
            'unlike' => $unlike,
            'transaction' => $transaction,
        ];
    }

    public function retweet(Tweet $tweet): array
    {
        $tweetClient = new TweetClient(
            apiKey: config('services.twitter.client_id'),
            apiSecret: config('services.twitter.client_secret'),
            accessToken: auth()->user()->oauth_token,
            accessSecret: auth()->user()->oauth_token_secret,
        );

        $retweet = $tweetClient->retweet(
            authUserId: auth()->user()->twitter_id,
            tweetId: $tweet->id
        );

        if (!$retweet) {
            throw new \Exception('Could not retweet tweet');
        }

        $retweet = auth()->user()->retweets()->create([
            'target_id' => $tweet->id,
        ]);

        $transaction = Transaction::create([
            'user_id' => auth()->user()->twitter_id,
            'type' => TransactionType::DEBIT,
            'amount' => config('pricing.retweet'),
            'description' => 'Retweeted Tweet ' . $tweet->id,
            'status' => TransactionStatus::FINAL,
        ]);

        return [
            'retweet' => $retweet,
            'transaction' => $transaction,
        ];
    }

    public function unretweet(Tweet $tweet): array
    {
        $tweetClient = new TweetClient(
            apiKey: config('services.twitter.client_id'),
            apiSecret: config('services.twitter.client_secret'),
            accessToken: auth()->user()->oauth_token,
            accessSecret: auth()->user()->oauth_token_secret,
        );

        $unretweet = $tweetClient->unretweet(
            authUserId: auth()->user()->twitter_id,
            tweetId: $tweet->id
        );

        if (!$unretweet) {
            throw new \Exception('Could not unretweet tweet');
        }

        $unretweet = auth()->user()->retweets()->where('target_id', $tweet->id)->delete();

        $transaction = Transaction::create([
            'user_id' => auth()->user()->twitter_id,
            'type' => TransactionType::DEBIT,
            'amount' => config('pricing.retweet'),
            'description' => 'Unretweeted Tweet ' . $tweet->id,
            'status' => TransactionStatus::FINAL,
        ]);

        return [
            'unretweet' => $unretweet,
            'transaction' => $transaction,
        ];
    }
}