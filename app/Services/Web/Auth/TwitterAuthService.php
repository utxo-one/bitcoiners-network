<?php

namespace App\Services\Web\Auth;

use App\Jobs\ProcessTwitterUser;
use Socialite;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use UtxoOne\TwitterUltimatePhp\Clients\UserClient;

class TwitterAuthService
{
    public function callback(): void
    {
        $user = Socialite::driver('twitter')->user();
        $userExists = User::where('twitter_id', $user->id)->first();

        if ($userExists) {
            if ($userExists->oauth_token !== $user->token) {
                $userExists->update([
                    'oauth_token' => $user->token,
                    'oauth_token_secret' => $user->tokenSecret,
                ]);
            }

            Auth::login($userExists);
        }

        $twitterUserClient = new UserClient(bearerToken: config('services.twitter.bearer_token'));
        $twitterUser = $twitterUserClient->getUserById($user->id);

        $userService = new UserService();
        $userService->saveTwitterUser($twitterUser);

        $newUser = User::find($twitterUser->getId());
        $newUser->update([
            'oauth_token' => $user->token,
            'oauth_token_secret' => $user->tokenSecret,
        ]);

        // If user hasn't been processed yet or hasn't been processed in 30 days, process them
        if (!$newUser->last_crawled_at || $newUser->last_crawled_at->diffInDays(now()) > 30) {
            $userService->processTwitterUser($twitterUser);
            $newUser->update([
                'last_crawled_at' => now(),
            ]);
        }

        Auth::login($newUser);
    }
}
