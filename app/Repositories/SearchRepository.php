<?php

namespace App\Repositories;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class SearchRepository
{

    public function autoFill(): Collection
    {
        if (strlen(request('q')) < 4) {
            throw new \Exception('Query must be at least 4 characters long');
        }

        return User::query()
            ->where('twitter_username', 'like', '%' . request('q') . '%')
            ->orderBy('twitter_count_followers', 'desc')
            ->get(['twitter_username', 'name', 'twitter_id']);
    }

    public function search(): SupportCollection
    {
        if (strlen(request('q')) < 4) {
            throw new \Exception('Query must be at least 4 characters long');
        }

        $user = User::where('twitter_username', request('q'))->first();

        if (!$user) {
            $user = User::query()
                ->where('twitter_username', 'like', '%' . request('q') . '%')
                ->orderBy('twitter_count_followers', 'desc')
                ->take(5)
                ->get();
        }

        $tweets = Tweet::query()
            ->where('text', 'like', '%' . request('q') . '%')
            ->with('user')
            ->orderBy('likes', 'desc')
            ->take(10)
            ->get();

        return collect([
            'users' => $user,
            'tweets' => $tweets,
        ]);
    }
}
