<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Get User Details
     * 
     * Get twitter user details by username.
     * 
     * @group User
     * 
     * @queryParam username string required The username of the user to get details for. Example: @utxo_one
     *
     * @return JsonResponse
     */
    public function show(string $username): JsonResponse
    {
        return response()->json([
            'user' => User::where('twitter_username', $username)->firstOrFail(),
        ]);
    }
}