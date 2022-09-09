<?php

namespace App\Services\Web\Auth;

use App\Enums\UserType;
use Socialite;

use App\Models\User;
use App\Services\UserService;
use Exception;
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

        $userService = new UserService();
        $userType = $userService->classifyUser($user->id);

        $twitterUserClient = new UserClient(bearerToken: config('services.twitter.bearer_token'));
        $twitterUser = $twitterUserClient->getUserById($user->id);

        $userService->saveTwitterUser($twitterUser, $userType);

        Auth::login($user);
    }
}
