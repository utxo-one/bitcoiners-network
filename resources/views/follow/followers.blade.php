<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Followers: ' . str()->plural(ucwords($userType->value))) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">

            <h1>We found {{ $followers->count() . ' ' . str()->plural($userType->value) }} you follow<br>
                @foreach ($followers as $user)
                    <li> <a href="https://twitter.com/{{ $user->twitter_username }}" target="_blank">{{ '@' . $user->twitter_username }}</a> </li>
                @endforeach


            </div>
        </div>
    </div>
</x-app-layout>
