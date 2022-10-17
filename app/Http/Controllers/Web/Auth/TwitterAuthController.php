<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Services\Web\Auth\TwitterAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Socialite;

class TwitterAuthController extends Controller
{
    public function __construct(private TwitterAuthService $twitterAuthService)
    {
    }

    /**
     * Redirect the user to the Twitter authentication page.
     * 
     * @group Authentication
     *
     * @return Response
     */
    public function login()
    {
        return Socialite::driver('twitter')->redirect();
    }

    /**
     * Obtain the user information from Twitter.
     * 
     * @group Authentication
     * @hideFromAPIDocumentation
     *
     * @return RedirectResponse
     */
    public function callback(): RedirectResponse
    {
        $this->twitterAuthService->callback();

        if (!auth()->check()) {
            return response()->json([
                'message' => 'Could not authenticate user',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->redirectTo('/u');
    }
}
