<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;

class FollowDataController extends Controller
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function show(string $username)
    {
        $user = User::where('twitter_username', $username)->firstOrFail();
        
        return response()->json($this->userRepository->getFollowData($user), Response::HTTP_OK);
    }
}
