<?php

namespace App\Services;

use App\Enums\FollowRequestStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Enums\UserType;
use App\Models\Follow;
use App\Models\FollowRequest;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use UtxoOne\TwitterUltimatePhp\Clients\UserClient;

class FollowService
{
    public function createFollowRequests(User $user, int $amount, ?UserType $userType = UserType::BITCOINER): Collection
    {
        $price = $amount * config('pricing.follow');

        if ($price > $user->getAvailableBalance()) {
            throw new \Exception('You don\'t have enough sats to follow this many users. Please top up your balance.');
        }

        // Create a follow request for each user that they don't already follow
        return User::where('type', $userType)
            ->whereDoesntHave('follows', function ($query) use ($user) {
                $query->where('followee_id', $user->twitter_id);
            })
            ->where('twitter_count_followers', '>', 500)
            ->where('twitter_count_followers', '<', 10000)
            ->where('twitter_count_following', '>', 500)
            ->where('type', $userType)
            ->orderBy('last_tweeted_at', 'desc')
            ->take($amount)
            ->get()
            ->map(function ($follow) use ($user) {
                return FollowRequest::create([
                    'user_id' => $user->twitter_id,
                    'follow_id' => $follow->twitter_id,
                    'status' => FollowRequestStatus::PENDING,
                ]);
            });
    }

    public function processFollowRequest(FollowRequest $followRequest): ?FollowRequest
    {
        // if the follow request is for this user, delete it and return null
        if ($followRequest->follow_id == $followRequest->user_id) {
            $followRequest->delete();
            return null;
        }

        // if the user already follows this user, delete the follow request and return null
        if ($followRequest->user->follows->where('followee_id', $followRequest->follow_id)->count() > 0) {
            $followRequest->delete();
            return null;
        }

        $client = new UserClient(
            apiKey: config('services.twitter.client_id'),
            apiSecret: config('services.twitter.client_secret'),
            accessToken: $followRequest->user->oauth_token,
            accessSecret: $followRequest->user->oauth_token_secret,
        );

        $response = $client->follow($followRequest->user->twitter_id, $followRequest->follow->twitter_id);

        if (!isset($response->getData()['following'])) {
            Log::error('Failed to follow user', [
                'follow_request_id' => $followRequest->id,
                'response' => $response->getData(),
            ]);
            throw new \Exception('Unable to follow user');
        }

        $followRequest->status = FollowRequestStatus::COMPLETED;
        $followRequest->completed_at = now();
        $followRequest->save();

        $transaction = Transaction::create([
            'user_id' => $followRequest->user->twitter_id,
            'type' => TransactionType::DEBIT,
            'amount' => config('pricing.follow'),
            'description' => 'Followed @' . $followRequest->follow->twitter_username,
            'status' => TransactionStatus::FINAL,
        ]);

        $followRequest->transaction_id = $transaction->id;
        $followRequest->save();

        // Create the follow record
        Follow::create([
            'follower_id' => $followRequest->user->twitter_id,
            'followee_id' => $followRequest->follow->twitter_id,
        ]);

        return $followRequest;
    }

    public function processFollowRequests()
    {
        // Process one follow request per user where the user has a pending follow request and no more than 100 completed follow requests in last 24 hours
        return User::whereHas('followRequests', function ($query) {
            $query->where('status', FollowRequestStatus::PENDING);
        })
            ->get()
            ->map(function ($user) {
                try {
                    // Check if the user already has more than 30-50 completed follow requests in the last 24 hours
                    $completedFollowRequests = $user->followRequests()
                        ->where('status', FollowRequestStatus::COMPLETED)
                        ->where('completed_at', '>', Carbon::now()->subDay())
                        ->count();

                    if ($completedFollowRequests >= rand(30, 50)) {
                        return null;
                    }

                    $this->processFollowRequest($user->followRequests()->where('status', FollowRequestStatus::PENDING)->first());
                } catch (\Exception $e) {
                    Log::error('Failed to process follow request', [
                        'user_id' => $user->twitter_id,
                        'error' => $e->getMessage(),
                    ]);

                    $user->followRequests()->where('status', FollowRequestStatus::PENDING)->first()->update([
                        'status' => FollowRequestStatus::REJECTED,
                        'completed_at' => now(),
                    ]);
                }
            });
    }

    public function getMassFollowSummary(): array
    {
        $output = [];

        // Get all the follow requests for the authenticated user
        $followRequests = FollowRequest::where('user_id', auth()->user()->twitter_id)
            ->get();

        // If the user doesn't have any pending or completed follow requests
        if ($followRequests->count() == 0) {
            $output['status'] = 'neverStarted';

            return $output;
        }

        // If the user has pending follow requests
        if (auth()->user()->followRequests()->where('status', FollowRequestStatus::PENDING)->exists()) {

            $completedCount = auth()->user()->followRequests()->where('status', FollowRequestStatus::COMPLETED)->count();
            $pendingCount = auth()->user()->followRequests()->where('status', FollowRequestStatus::PENDING)->count();

            $output['totalCompletedFollowRequests'] = $completedCount;
            $output['totalSpentSats'] = $completedCount * config('pricing.follow');
            $output['estimatedCompletionDays'] = $pendingCount / config('limits.dailyFollows');
            $output['pendingFollowRequests'] = $pendingCount;
            $output['status'] = 'running';

            return $output;
        }

        // If the user doesn't have pending follow requests, but has completed follow requests
        if (
            !auth()->user()->followRequests()->where('status', FollowRequestStatus::PENDING)->exists() &&
            auth()->user()->followRequests()->where('status', FollowRequestStatus::COMPLETED)->exists()
        ) {

            $output['totalCompletedFollowRequests'] = auth()->user()->followRequests()->where('status', FollowRequestStatus::COMPLETED)->count();
            $output['totalSpentSats'] = auth()->user()->followRequests()->where('status', FollowRequestStatus::COMPLETED)->count() * config('pricing.follow');
            $output['estimatedCompletionDays'] = 0;
            $output['pendingFollowRequests'] = 0;
            $output['status'] = 'paused';

            return $output;
        }
    }

    public function followUser(User $user): array
    {
        // check if the user has enough availableBalance() to follow this user
        if (auth()->user()->getAvailableBalance() < config('pricing.follow')) {
            throw new \Exception('You don\'t have enough sats to follow this user. Please top up your balance.');
        }

        if (auth()->user()->follows()->where('followee_id', $user->twitter_id)->exists()) {
            throw new \Exception('You are already following this user.');
        }

        $userClient = new UserClient(
            apiKey: config('services.twitter.client_id'),
            apiSecret: config('services.twitter.client_secret'),
            accessToken: auth()->user()->oauth_token,
            accessSecret: auth()->user()->oauth_token_secret,
        );

        $response = $userClient->follow(auth()->user()->twitter_id, $user->twitter_id);

        if (!isset($response->getData()['following'])) {
            Log::error('Failed to follow user', [
                'user_id' => auth()->user()->twitter_id,
                'follow_id' => $user->twitter_id,
                'response' => $response->getData(),
            ]);
            throw new \Exception('Unable to follow user');
        }

        $transaction = Transaction::create([
            'user_id' => auth()->user()->twitter_id,
            'type' => TransactionType::DEBIT,
            'amount' => config('pricing.follow'),
            'description' => 'Followed @' . $user->twitter_username,
            'status' => TransactionStatus::FINAL,
        ]);

        $follow = Follow::create([
            'follower_id' => auth()->user()->twitter_id,
            'followee_id' => $user->twitter_id,
        ]);

        return [
            'follow' => $follow,
            'transaction' => $transaction,
        ];
    }

    public function unfollowUser(User $user): array
    {
        // check if the user has enough availableBalance() to follow this user
        if (auth()->user()->getAvailableBalance() < config('pricing.follow')) {
            throw new \Exception('You don\'t have enough sats to unfollow this user. Please top up your balance.');
        }

        if (!auth()->user()->follows()->where('followee_id', $user->twitter_id)->exists()) {
            throw new \Exception('You are not following this user.');
        }

        $userClient = new UserClient(
            apiKey: config('services.twitter.client_id'),
            apiSecret: config('services.twitter.client_secret'),
            accessToken: auth()->user()->oauth_token,
            accessSecret: auth()->user()->oauth_token_secret,
        );

        $response = $userClient->unfollow(auth()->user()->twitter_id, $user->twitter_id);

        if (!isset($response->getData()['following'])) {
            Log::error('Failed to unfollow user', [
                'user_id' => auth()->user()->twitter_id,
                'follow_id' => $user->twitter_id,
                'response' => $response->getData(),
            ]);
            throw new \Exception('Unable to unfollow user');
        }

        $transaction = Transaction::create([
            'user_id' => auth()->user()->twitter_id,
            'type' => TransactionType::DEBIT,
            'amount' => config('pricing.unfollow'),
            'description' => 'Unfollowed @' . $user->twitter_username,
            'status' => TransactionStatus::FINAL,
        ]);

        $follow = Follow::where('follower_id', auth()->user()->twitter_id)
            ->where('followee_id', $user->twitter_id)
            ->first();

        $follow->delete();

        return [
            'transaction' => $transaction,
        ];
    }
}
