<?php

namespace App\Services\Web\Auth;

use App\Enums\UserType;
use Socialite;

use App\Models\User;
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

        $type = $this->classifyUser($user->id);

        $user = User::create([
            'name' => $user->name,
            'email' => $user->email,
            'twitter_id'=> $user->id,
            'type' => $type,
            'oauth_type'=> 'twitter',
            'oauth_token'=> $user->token,
            'oauth_token_secret'=> $user->tokenSecret,
            'password' => encrypt(str()->random(10))
        ]);

        Auth::login($user);
    }

    public function classifyUser(string $twitterId): UserType
    {
        $twitterUserClient = new UserClient(bearerToken: config('services.twitter.bearer_token'));

        $twitterUser = $twitterUserClient->getUserById($twitterId);

        $twitterBioWordCollection = collect(explode(' ', $twitterUser->getDescription()));
        $bitcoinerKeywords = collect(config('classifier.bitcoinerKeywords'));
        $shitcoinerKeywords = collect(config('classifier.shitcoinerKeywords'));

        $bioContainsShitcoinKeywords = $twitterBioWordCollection->contains(function ($word) use ($shitcoinerKeywords) {
            return $shitcoinerKeywords->contains($word);
        });

        $bioContainsBitcoinKeywords = $twitterBioWordCollection->contains(function ($word) use ($bitcoinerKeywords) {
            return $bitcoinerKeywords->contains($word);
        });

        $nameContainsShitcoins = collect(config('classifier.shitcoinerNames'))->contains(function ($shitcoinName) use ($twitterUser) {
            return str_contains($twitterUser->getName(), $shitcoinName);
        });

        $nameContainsBitcoin = collect(config('classifier.bitcoinerNames'))->contains(function ($bitcoinName) use ($twitterUser) {
            return str_contains($twitterUser->getName(), $bitcoinName);
        });

        $type = UserType::NOCOINER;

        if ($bioContainsShitcoinKeywords || $nameContainsShitcoins) {
            $type = UserType::SHITCOINER;
        }

        if ($bioContainsBitcoinKeywords || $nameContainsBitcoin && !$bioContainsShitcoinKeywords && !$nameContainsShitcoins) {
            $type = UserType::BITCOINER;
        }

        return $type;
    }
}
