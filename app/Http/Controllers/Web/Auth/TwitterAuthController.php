<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Services\Web\Auth\TwitterAuthService;
use Illuminate\Http\Request;
use Socialite;
use UtxoOne\TwitterUltimatePhp\Clients\UserClient;

class TwitterAuthController extends Controller
{
    public function __construct(private TwitterAuthService $twitterAuthService)
    {
    }

    public function login()
    {
        return Socialite::driver('twitter')->redirect();
    }

    public function callback()
    {
        $this->twitterAuthService->callback();

        $twitterUserClient = new UserClient(bearerToken: config('services.twitter.bearer_token'));

        $twitterUser = $twitterUserClient->getUserById(auth()->user()->twitter_id);

        return view('dashboard', [
            'twitterUser' => $twitterUser,
        ]);
    }
}
