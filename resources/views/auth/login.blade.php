<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="flex items-center justify-end mt-4">
                <a class="btn" href="{{ url('auth/twitter') }}"
                    style="background: #1E9DEA; padding: 10px; width: 100%; text-align: center; display: block; border-radius:4px; color: #ffffff;">
                    Login with Twitter
                </a>
            </div>

        </form>
    </x-jet-authentication-card>
</x-guest-layout>
