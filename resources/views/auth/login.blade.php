

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="flex items-center justify-end mt-4">
                <a class="btn" href="{{ url('auth/twitter') }}"
                    style="background: #1E9DEA; padding: 10px; width: 100%; text-align: center; display: block; border-radius:4px; color: #ffffff;">
                    Login with Twitter
                </a>
            </div>
