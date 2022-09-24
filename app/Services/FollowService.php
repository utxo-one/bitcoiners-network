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
            ->inRandomOrder()
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

    public function processFollowRequest(FollowRequest $followRequest):? FollowRequest
    {
        // if the follow request is for this user, delete it and return null
        if ($followRequest->follow_id == $followRequest->user_id) {
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

    public function processFollowRequests(): Collection
    {
        // Process one follow request per user where the user has a pending follow request
        return User::whereHas('followRequests', function ($query) {
            $query->where('status', FollowRequestStatus::PENDING);
        })
            ->get()
            ->map(function ($user) {
                return $this->processFollowRequest($user->followRequests()->where('status', FollowRequestStatus::PENDING)->first());
            });
    }

    public function getMassFollowSummary(): array
    {
        $output = [];

        // If the user has pending follow requests
        if (auth()->user()->followRequests()->where('status', FollowRequestStatus::PENDING)->exists()) {
            $output['totalCompletedFollowRequests'] = auth()->user()->followRequests()->where('status', FollowRequestStatus::COMPLETED)->count();
            $output['totalSpentSats'] = auth()->user()->followRequests()->where('status', FollowRequestStatus::COMPLETED)->count() * config('pricing.follow');
            $output['estimatedCompletionDays'] = auth()->user()->followRequests()->where('status', FollowRequestStatus::PENDING)->count() / config('limits.dailyFollows');
            $output['pendingFollowRequests'] = auth()->user()->followRequests()->where('status', FollowRequestStatus::PENDING)->count();
            $output['status'] = 'running';

            $output['recentCompletedFollows'] = auth()->user()->followRequests()->with('follow')->where('status', FollowRequestStatus::COMPLETED)->orderBy('completed_at', 'desc')->take(10)->get();

            return $output;
        }

        // If the user doesn't have any pending or completed follow requests
        if (!auth()->user()->followRequests()->exists()) {
            $output['status'] = 'neverStarted';

            return $output;
        }

        // If the user doesn't have pending follow requests, but has completed follow requests
        if (!auth()->user()->followRequests()->where('status', FollowRequestStatus::PENDING)->exists() && 
            auth()->user()->followRequests()->where('status', FollowRequestStatus::COMPLETED)->exists()) {
            $output['totalCompletedFollowRequests'] = auth()->user()->followRequests()->where('status', FollowRequestStatus::COMPLETED)->count();
            $output['totalSpent'] = auth()->user()->followRequests()->where('status', FollowRequestStatus::COMPLETED)->count() * config('pricing.follow');
            $output['estimated_completion_time_days'] = 0;
            $output['pendingFollowRequests'] = 0;
            $output['status'] = 'paused';

            $output['recentCompletedFollows'] = auth()->user()->followRequests()->with('follow')->where('status', FollowRequestStatus::COMPLETED)->orderBy('completed_at', 'desc')->take(10)->get();

            return $output;
        }
    }
}
