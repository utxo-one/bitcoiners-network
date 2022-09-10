<?php

namespace App\Http\Controllers\Web\Follow\Scopes;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FollowerController extends Controller
{
    public function index(UserType $userType)
    {
        $followers = auth()->user()->getFollowersByType($userType);

        return view('follow.followers', [
            'followers' => $followers,
            'userType' => $userType,
        ]);
    }
}
