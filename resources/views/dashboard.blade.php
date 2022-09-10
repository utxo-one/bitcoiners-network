<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
            Available Balance: {{ auth()->user()->getAvailableBalance() }}
                You follow {{ auth()->user()->twitter_count_following }} people and {{ auth()->user()->twitter_count_followers }} people follow you.<br><br>

                Bitcoiner Followers: <a class="text-orange-500 underline" href="{{ route('follow.followers', \App\Enums\UserType::BITCOINER) }}">{{ auth()->user()->getFollowersByType(\App\Enums\UserType::BITCOINER)->count() }}</a><br>
                Bitcoiner Following: <a class="text-orange-500 underline" href="{{ route('follow.following', \App\Enums\UserType::BITCOINER) }}">{{ auth()->user()->getFollowingByType(\App\Enums\UserType::BITCOINER)->count() }}</a><br><br>

                Shitcoiner Followers: <a class="text-orange-500 underline" href="{{ route('follow.followers', \App\Enums\UserType::SHITCOINER) }}">{{ auth()->user()->getFollowersByType(\App\Enums\UserType::SHITCOINER)->count() }}</a><br>
                Shitcoiner Following: <a class="text-orange-500 underline" href="{{ route('follow.following', \App\Enums\UserType::SHITCOINER) }}">{{ auth()->user()->getFollowingByType(\App\Enums\UserType::SHITCOINER)->count() }}</a><br><br>

                NoCoiner Followers: <a class="text-orange-500 underline" href="{{ route('follow.followers', \App\Enums\UserType::NOCOINER) }}">{{ auth()->user()->getFollowersByType(\App\Enums\UserType::NOCOINER)->count() }}</a><br>
                NoCoiner Following: <a class="text-orange-500 underline" href="{{ route('follow.following', \App\Enums\UserType::NOCOINER) }}">{{ auth()->user()->getFollowingByType(\App\Enums\UserType::NOCOINER)->count() }}</a><br><br>

                Bitcoiners Available to Follow: <a class="text-orange-500 underline" href="{{ route('follow.available', \App\Enums\UserType::BITCOINER) }}">{{ auth()->user()->getAvailableFollows(\App\Enums\UserType::BITCOINER)->count() }}</a><br>

            </div>
        </div>
    </div>
</x-app-layout>
