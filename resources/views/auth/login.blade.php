<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
            @if (request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.*'))
                <h1 class="mx-auto py-2 text-white">Administration space</h1>
            @endif
        </x-slot>
        <div class="flex mb-2 items-center justify-end text-black">
            @foreach (\App\Models\SettingLocal::getLangs() as $key => $value)
                <a class="mr-2 items-center justify-center" style="height:35px" href="/lang/{{ $key }}">
                    <img src="{{ asset('images/' . $key . '.svg') }}" alt="{{ $value }}">
                </a>
            @endforeach
        </div>
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST">
            @csrf

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autofocus />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />

                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="current-password" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has(\App\Models\SettingLocal::getLang() . '.password.request') &&
                        request()->routeIs(\App\Models\SettingLocal::getLang() . '.login'))
                    <a class="underline text-sm text-gray-400 hover:text-gray-100"
                        href="{{ route(\App\Models\SettingLocal::getLang() . '.password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ml-3">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
    <footer class="absolute w-full bottom-0">
        <div class="footer-container">
            <a href="/terms-and-conditions" class="footer-link" target="_blank">{{ __('Terms and Conditions') }}</a>
            <a href="/privacy-policy" class="footer-link" target="_blank">{{ __('Privacy Policy') }}</a>
            <a href="/earnings-disclaimer" class="footer-link" target="_blank">{{ __('Earnings Disclaimer') }}</a>
        </div>
    </footer>

    <style>
        .footer-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 16px;
            text-align: center;
            margin: 0 auto;
            padding: 16px;
        }

        .footer-link {
            text-decoration: none;
            color: #4B5563;
            /* text-gray-600 */
            margin: 8px 0;
        }

        @media (max-width: 600px) {
            .footer-container {
                flex-direction: column;
                gap: 0px;
                padding: 0;
                margin-bottom: 5px;
                font-size: 14px;
            }

            .footer-link {
                text-decoration: none;
                color: #4B5563;
                /* text-gray-600 */
                margin: 0;
            }
        }
    </style>
</x-guest-layout>
