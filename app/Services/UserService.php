<?php

namespace App\Services;

use App\Enums\ClassificationSource;
use App\Enums\FollowChunkType;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Enums\UserType;
use App\Models\Block;
use App\Models\ClassificationVote;
use App\Models\Endorsement;
use App\Models\Follow;
use App\Models\FollowChunk;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Log;
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

        if (!$twitterUser && !$user) {
            $twitterUserClient = new UserClient(bearerToken: config('services.twitter.bearer_token'));
            $twitterUser = $twitterUserClient->getUserById($twitterId);
        }

        // if we are passed a twitterUser, check if a user model already exists, if so, use that model for the method.
        if ($twitterUser) {
            $tryUser = User::where('twitter_id', $twitterUser->getId())->first();
            if ($tryUser) {
                $user = $tryUser;
            }
        }

        // if the classification source is vote, do not reclassify and return the current type
        if ($user) {

            if ($user->classified_by === ClassificationSource::VOTE->value) {
                switch ($user->type) {
                    case UserType::BITCOINER->value:
                        $type = UserType::BITCOINER;
                        break;
                    case UserType::SHITCOINER->value:
                        $type = UserType::SHITCOINER;
                        break;
                    case UserType::NOCOINER->value:
                        $type = UserType::NOCOINER;
                        break;
                }

                return $type;
            }

            if ($user->lightning_verified == true) {
                return UserType::BITCOINER;
            }
        }

        $bio = ($user) ? $user->twitter_description : $twitterUser->getDescription();
        $username = ($user) ? $user->name : $twitterUser->getName();

        $bioWords = collect(explode(' ', $bio));

        $bitcoinerKeywords = collect(config('classifier.bitcoinerKeywords'));
        $shitcoinerKeywords = collect(config('classifier.shitcoinerKeywords'));

        $bioContainsShitcoinKeywords = $bioWords
            ->contains(function ($word) use ($shitcoinerKeywords) {
                return $shitcoinerKeywords->contains(
                    str_replace(['.', ','], '', $word)
                );
            });

        $bioContainsBitcoinKeywords = $bioWords
            ->contains(function ($word) use ($bitcoinerKeywords) {
                return $bitcoinerKeywords->contains(
                    str_replace(['.', ','], '', $word)
                );
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

    public function refreshUser(User $user): User
    {
        $twitterUserClient = new UserClient(bearerToken: config('services.twitter.bearer_token'));
        $twitterUser = $twitterUserClient->getUserById($user->twitter_id);

        $user->update([
            'name' => $twitterUser->getName(),
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
            'last_refreshed_at' => Carbon::now(),
        ]);

        return $user->fresh();
    }

    public function saveTwitterUser(TwitterUser $twitterUser): User
    {
        // Check if a user already exists with this username but a different twitter id
        $user = User::query()
            ->where('twitter_username', $twitterUser->getUsername())
            ->whereNot('twitter_id', $twitterUser->getId())
            ->first();


        if ($user) {
            try {
                $this->refreshUser($user);
            } catch (Exception $e) {
                $user->delete();
            }
        }


        $user = User::query()->firstOrNew(['twitter_id' => $twitterUser->getId()]);

        if ($user->exists) {

            // if the user has alraedy been updated in the last 30 days, just return it
            if ($user->last_refreshed_at && $user->last_refreshed_at->isAfter(Carbon::now()->subDays(30))) {
                return $user;
            }

            $user->update([
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
                'oauth_type' => 'twitter',
                'password' => encrypt(str()->random(10)),
                'classified_by' => ($user->classified_by != ClassificationSource::CRAWLER) ? $user->classified_by : ClassificationSource::CRAWLER,
                'last_classified_at' => Carbon::now(),
                'last_refreshed_at' => Carbon::now(),
            ]);

            return $user->fresh();
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
            'oauth_type' => 'twitter',
            'password' => encrypt(str()->random(10)),
            'classified_by' => ClassificationSource::CRAWLER,
            'last_classified_at' => Carbon::now(),
            'last_refreshed_at' => Carbon::now(),
        ]);
    }

    public function saveTwitterUsers(array $twitterUsers): array
    {
        $users = [];

        foreach ($twitterUsers as $twitterUser) {
            $users[] = $this->saveTwitterUser($twitterUser);
        }

        return $users;
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

    public function chunkFollows(TwitterUser $twitterUser): ?SupportCollection
    {
        $followingCount = $twitterUser->getPublicMetrics()->getFollowingCount();
        $followerCount = $twitterUser->getPublicMetrics()->getFollowersCount();

        $chunkSize = 1000;

        $followingChunkCount = ceil($followingCount / $chunkSize);
        $followerChunkCount = ceil($followerCount / $chunkSize);

        $followChunks = [];

        for ($i = 0; $i < $followingChunkCount; $i++) {
            $followChunks[] = FollowChunk::create([
                'user_id' => $twitterUser->getId(),
                'type' => FollowChunkType::FOLLOWING,
            ]);
        }

        for ($i = 0; $i < $followerChunkCount; $i++) {
            $followChunks[] = FollowChunk::create([
                'user_id' => $twitterUser->getId(),
                'type' => FollowChunkType::FOLLOWER,
            ]);
        }

        return collect($followChunks);
    }

    public function processFollowChunk(FollowChunk $followChunk): SupportCollection
    {
        $typeMethod = $followChunk->type === FollowChunkType::FOLLOWING->value ? 'getFollowing' : 'getFollowers';

        $userClient = new UserClient(bearerToken: config('services.twitter.bearer_token'));

        $paginationToken = $followChunk->user()->first()->last_pagination_token;

        $follows = $userClient->$typeMethod(
            id: $followChunk->user_id,
            maxResults: 1000,
            paginationToken: $paginationToken,
        );

        $users = [];

        foreach ($follows->all() as $follow) {

            $users[] = $this->saveTwitterUser($follow);

            if ($followChunk->type === FollowChunkType::FOLLOWING->value) {
                $followeeExists = Follow::query()
                    ->where('followee_id', $follow->getId())
                    ->where('follower_id', $followChunk->user_id)
                    ->first();

                if (!$followeeExists) {
                    Follow::create([
                        'followee_id' => $follow->getId(),
                        'follower_id' => $followChunk->user_id,
                    ]);
                }
            } else {
                $followerExists = Follow::query()
                    ->where('followee_id', $followChunk->user_id)
                    ->where('follower_id', $follow->getId())
                    ->first();

                if (!$followerExists) {
                    Follow::create([
                        'followee_id' => $followChunk->user_id,
                        'follower_id' => $follow->getId(),
                    ]);
                }
            }
        }

        // If a pagination token exists, update the user's last_pagination_token, else set it to blank
        if ($follows->getPaginationToken() !== null) {
            $followChunk->user()->update([
                'last_pagination_token' => $follows->getPaginationToken(),
            ]);
        } else {
            $followChunk->user()->update([
                'last_pagination_token' => null,
            ]);
        }

        $followChunk->update([
            'processed_at' => Carbon::now(),
        ]);

        return collect($users);
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

        $chunks = $this->chunkFollows($twitterUser);

        Log::info('Processing ' . $chunks->count() . ' follow chunks for user ' . $twitterUser->getId());

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

    public function endorseUser(string $endorserId, string $endorseeId, string $type): bool
    {
        $endorsement = Endorsement::query()
            ->where('endorser_id', $endorserId)
            ->where('endorsee_id', $endorseeId)
            ->where('endorsement_type', $type)
            ->first();

        if ($endorsement) {
            return false;
        }

        Endorsement::create([
            'endorser_id' => $endorserId,
            'endorsee_id' => $endorseeId,
            'endorsement_type' => $type,
        ]);

        return true;
    }

    public function unendorseUser(string $endorserId, string $endorseeId, string $type): bool
    {
        $endorsement = Endorsement::query()
            ->where('endorser_id', $endorserId)
            ->where('endorsee_id', $endorseeId)
            ->where('endorsement_type', $type)
            ->first();

        if (!$endorsement) {
            return false;
        }

        $endorsement->delete();

        return true;
    }

    public function voteClassifiction(User $user, string $type): ClassificationVote
    {
        if ($type !== 'bitcoiner' && $type !== 'shitcoiner' && $type !== 'nocoiner') {
            throw new \Exception('Invalid classification type');
        }

        $classificationVote = ClassificationVote::query()
            ->where('classified_id', $user->twitter_id)
            ->where('classifier_id', auth()->user()->twitter_id)
            ->first();

        if ($classificationVote) {
            $classificationVote->delete();
        }

        $vote = ClassificationVote::create([
            'classified_id' => $user->twitter_id,
            'classifier_id' => auth()->user()->twitter_id,
            'classification_type' => $type,
        ]);

        if ($user->classificationVotesReceived()->count() >= 3) {
            $this->classifyUserByVotes($user);
        }

        return $vote;
    }

    public function unvoteClassifiction(User $user): bool
    {
        $classificationVote = ClassificationVote::query()
            ->where('classified_id', $user->twitter_id)
            ->where('classifier_id', auth()->user()->twitter_id)
            ->first();

        if (!$classificationVote) {
            return false;
        }

        $classificationVote->delete();

        return true;
    }

    public function classifyUserByVotes(User $user)
    {
        $votes = $user->classificationVotesReceived()->get();

        $voteCounts = [
            'bitcoiner' => 0,
            'shitcoiner' => 0,
            'nocoiner' => 0,
        ];

        foreach ($votes as $vote) {
            $voteCounts[$vote->classification_type]++;
        }

        // Get the highest vote count and the array key
        $highestVoteCount = max($voteCounts);
        $highestVoteType = array_search($highestVoteCount, $voteCounts);

        $user->type = $highestVoteType;
        $user->last_classified_at = Carbon::now();
        $user->classified_by = ClassificationSource::VOTE;
        $user->save();
    }

    public function blockUser(User $user): array
    {
        $userClient = new UserClient(
            apiKey: config('services.twitter.client_id'),
            apiSecret: config('services.twitter.client_secret'),
            accessToken: auth()->user()->oauth_token,
            accessSecret: auth()->user()->oauth_token_secret,
        );

        $block = $userClient->block(
            authUserId: auth()->user()->twitter_id,
            userId: $user->twitter_id,
        )->getData();

        if ($block['blocking'] !== true) {
            throw new \Exception('Failed to block user');
        }

        $block = auth()->user()->blocks()->create([
            'target_id' => $user->twitter_id,
        ]);

        $transaction = Transaction::create([
            'user_id' => auth()->user()->twitter_id,
            'type' => TransactionType::DEBIT,
            'amount' => config('pricing.block'),
            'description' => 'Blocked @' . $user->twitter_username,
            'status' => TransactionStatus::FINAL,
        ]);

        return [
            'block' => $block,
            'transaction' => $transaction,
        ];
    }

    public function unblockUser(User $user): array
    {
        $userClient = new UserClient(
            apiKey: config('services.twitter.client_id'),
            apiSecret: config('services.twitter.client_secret'),
            accessToken: auth()->user()->oauth_token,
            accessSecret: auth()->user()->oauth_token_secret,
        );

        $block = $userClient->unblock(
            authUserId: auth()->user()->twitter_id,
            userId: $user->twitter_id,
        )->getData();

        if ($block['blocking'] !== false) {
            throw new \Exception('Failed to unblock user');
        }

        $block = auth()->user()->blocks()->where('target_id', $user->twitter_id)->first();

        if (!$block) {
            throw new \Exception('Failed to find block');
        }

        $block->delete();

        $transaction = Transaction::create([
            'user_id' => auth()->user()->twitter_id,
            'type' => TransactionType::DEBIT,
            'amount' => config('pricing.block'),
            'description' => 'Unblocked @' . $user->twitter_username,
            'status' => TransactionStatus::FINAL,
        ]);

        return [
            'block' => $block,
            'transaction' => $transaction,
        ];
    }

    public function muteUser(User $user): array
    {
        $userClient = new UserClient(
            apiKey: config('services.twitter.client_id'),
            apiSecret: config('services.twitter.client_secret'),
            accessToken: auth()->user()->oauth_token,
            accessSecret: auth()->user()->oauth_token_secret,
        );

        $mute = $userClient->mute(
            authUserId: auth()->user()->twitter_id,
            userId: $user->twitter_id,
        )->getData();

        if ($mute['muting'] !== true) {
            throw new \Exception('Failed to mute user');
        }

        $mute = auth()->user()->mutes()->create([
            'target_id' => $user->twitter_id,
        ]);

        $transaction = Transaction::create([
            'user_id' => auth()->user()->twitter_id,
            'type' => TransactionType::DEBIT,
            'amount' => config('pricing.mute'),
            'description' => 'Muted @' . $user->twitter_username,
            'status' => TransactionStatus::FINAL,
        ]);

        return [
            'mute' => $mute,
            'transaction' => $transaction,
        ];
    }

    public function unmuteUser(User $user): array
    {
        $userClient = new UserClient(
            apiKey: config('services.twitter.client_id'),
            apiSecret: config('services.twitter.client_secret'),
            accessToken: auth()->user()->oauth_token,
            accessSecret: auth()->user()->oauth_token_secret,
        );

        $mute = $userClient->unmute(
            authUserId: auth()->user()->twitter_id,
            userId: $user->twitter_id,
        )->getData();

        if ($mute['muting'] !== false) {
            throw new \Exception('Failed to unmute user');
        }

        $mute = auth()->user()->mutes()->where('target_id', $user->twitter_id)->first();

        if (!$mute) {
            throw new \Exception('Failed to find mute');
        }

        $mute->delete();

        $transaction = Transaction::create([
            'user_id' => auth()->user()->twitter_id,
            'type' => TransactionType::DEBIT,
            'amount' => config('pricing.mute'),
            'description' => 'Unmuted @' . $user->twitter_username,
            'status' => TransactionStatus::FINAL,
        ]);

        return [
            'mute' => $mute,
            'transaction' => $transaction,
        ];
    }

    public function getBlocks(User $user): SupportCollection
    {
        $userClient = new UserClient(
            apiKey: config('services.twitter.client_id'),
            apiSecret: config('services.twitter.client_secret'),
            accessToken: auth()->user()->oauth_token,
            accessSecret: auth()->user()->oauth_token_secret,
        );

        $blocks = $userClient->getBlocks(
            id: auth()->user()->twitter_id,
        );

        return collect($blocks);
    }
}
