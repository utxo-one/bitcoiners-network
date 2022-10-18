<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Services\Web\Auth\TwitterAuthService;
use Carbon\Carbon;
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

        // If the first_login_at field is within the last 30 seconds, redirect to the onboarding flow
        $firstLogin = Carbon::create(auth()->user()->first_login_at);
        $thirtySecondsAgo = Carbon::now()->subSeconds(30);

        $query = http_build_query([
            'firstLogin' => false,
        ]);

        if ($firstLogin->greaterThan($thirtySecondsAgo)) {
            $query = http_build_query([
                'firstLogin' => true,
            ]);
        }     

        return response()->redirectTo('/u/dashboard?' . $query);
    }

    /**
     * Log the user out of the application.
     * 
     * @group Authentication
     *
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        auth()->logout();

        return Redirect::to('/');
    }
}
