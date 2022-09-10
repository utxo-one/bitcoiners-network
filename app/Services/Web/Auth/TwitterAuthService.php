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
            Auth::login($userExists);
        }

        $twitterUserClient = new UserClient(bearerToken: config('services.twitter.bearer_token'));
        $twitterUser = $twitterUserClient->getUserById($user->id);

        $userService = new UserService();
        $userService->saveTwitterUser($twitterUser);

        $user = User::find($twitterUser->getId());

        ProcessTwitterUser::dispatch($twitterUser);

        Auth::login($user);
    }
}
