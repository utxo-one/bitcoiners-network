<?php

namespace App\Http\Controllers\Web\Follow\Scopes;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FollowingController extends Controller
{
    public function index(UserType $userType)
    {
        $following = auth()->user()->getFollowingByType($userType);

        return view('follow.following', [
            'following' => $following,
            'userType' => $userType,
        ]);
    }
}
