<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    public function profilesPictures()
    {
        $profilePictureUrls = User::where('type', UserType::BITCOINER)
            ->inRandomOrder()
            ->whereNotNull('twitter_profile_image_url')
            ->pluck('twitter_profile_image_url')
            ->take(100)
            ->toArray();

        $highResProfilePictureUrls = array_map(function ($url) {
            return str_replace('_normal', '', $url);
        }, $profilePictureUrls);

        return response()->json($highResProfilePictureUrls, Response::HTTP_OK);
    }

    public function randomBitcoiners()
    {
        return response()->json(
            User::where('type', UserType::BITCOINER)
                ->inRandomOrder()
                ->whereNotNull('twitter_profile_image_url')
                ->take(10)
                ->get(),
            Response::HTTP_OK);
    }

    public function randomShitcoiners()
    {
        return response()->json(
            User::where('type', UserType::SHITCOINER)
                ->inRandomOrder()
                ->whereNotNull('twitter_profile_image_url')
                ->take(10)
                ->get(),
            Response::HTTP_OK);
    }

    public function randomNocoiners()
    {
        return response()->json(
            User::where('type', UserType::NOCOINER)
                ->inRandomOrder()
                ->whereNotNull('twitter_profile_image_url')
                ->take(10)
                ->get(),
            Response::HTTP_OK);
    }

    
}
