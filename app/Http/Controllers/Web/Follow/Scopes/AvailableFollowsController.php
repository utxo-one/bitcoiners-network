<?php

namespace App\Http\Controllers\Web\Follow\Scopes;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AvailableFollowsController extends Controller
{
    public function index(UserType $userType)
    {
        $availableFollows = auth()->user()->getAvailableFollows($userType);

        return view('follow.available', [
            'availableFollows' => $availableFollows,
            'userType' => $userType,
        ]);
    }
}
