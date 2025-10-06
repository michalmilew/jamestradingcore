<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" type="image/ico" href="https://jamestradinggroup.com/images/favicon.ico">
    <!-- For modal -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.css" rel="stylesheet" />

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.15/tailwind.min.css" />

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.js"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <style>
        .bg-green-perso {
            background-color: #0CAF60;
        }

        .bg-dark-1 {
            background-color: #12181F;
        }

        .bg-dark-2 {
            background-color: #12181F;
            padding: 5px;
        }

        .bg-dark-3 {
            background-color: #0CAF60;
        }

        select {
            background-color: #12181F;
        }

        [type="text"],
        [type="email"],
        [type="url"],
        [type="password"],
        [type="number"],
        [type="date"],
        [type="datetime-local"],
        [type="month"],
        [type="search"],
        [type="tel"],
        [type="time"],
        [type="week"],
        [multiple],
        textarea,
        select {
            background-color: #12181F;
            border-radius: 10px;
            padding: .4em;
        }

        @media (max-width: 768px) {
            .mobile-header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 50;
                background-color: #282e37;
            }
            
            .mobile-content {
                padding-top: 6rem;
            }
            
            main {
                padding-top: 6rem;
            }
            
            .mobile-nav {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                overflow: hidden;
                z-index: 40;
            }
        }

        @media (min-width: 769px) {
            .mobile-header {
                position: static;
                background-color: transparent;
            }
            
            main {
                padding-top: 0;
            }
            
            .mobile-content {
                padding-top: 0;
            }
        }
    </style>
    @stack('styles')
</head>

<body class="font-sans antialiased text-gray-200" style="background-color:#282e37">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="min-h-screen pr-2 flex flex-col w-64 hidden md:block" style="padding-left:16px; background:#12181F">
            <!-- Logo -->
            <div class="shrink-0 flex items-center">
                @if (auth()->guard('admin')->check())
                    <a href="{{ route(\App\Models\SettingLocal::getLang() . '.admin.dashboard') }}">
                        <x-application-logo class="block w-150 fill-current text-gray-600" />
                    </a>
                @else
                    <a href="{{ route(\App\Models\SettingLocal::getLang() . '.client.accounts.index') }}">
                        <x-application-logo class="block w-150 fill-current text-gray-600" />
                    </a>
                @endif
            </div>
            <ul class="flex flex-col items-start mt-16">
                @if (auth()->guard('admin')->check())
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.dashboard')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.admins.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.admins*')">
                        {{ __('Administrators') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.users.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.users*')">
                        {{ __('Users') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.accounts.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.accounts.*')">
                        {{ __('Metatrader Accounts') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.pnl.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.pnl.*')">
                        {{ __('Pnl') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.servers.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.servers*')">
                        {{ __('Servers') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.courses.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.courses.*')">
                        {{ __('Courses') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.notification.test')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.notification.test')">
                        {{ __('Email Broadcast Test') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.notifications.select')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.notifications.*')">
                        {{ __('Email Text Edit') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.cleanup.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.cleanup.*')">
                        {{ __('Cron Setting') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.risk-settings.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.risk-settings.*')">
                        {{ __('Calculator') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.languages.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.languages.*')">
                        {{ __('Language') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.notification-rules.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.notification-rules.*')">
                        {{ __('Notification Rule') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.telegram-settings.index', ['language' => \App\Models\SettingLocal::getLang()])" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.telegram-settings.*')">
                        {{ __('Telegram Settings') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.affiliates.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.affiliates.*')">
                        {{ __('Affiliates') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.metatrader.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.metatrader.*')">
                        {{ __('MetaTrader Management') }}
                    </x-nav-link>
                @else
                    @if (Auth::user()->restricted_user !== 1)
                        <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.client.accounts.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.client.accounts.*')">
                            {{ __('MetaTrader 4 Account') }}
                        </x-nav-link>
                        <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.client.reports.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.client.reports.*')">
                            {{ __('Report') }}
                        </x-nav-link>
                    @endif
                    @if (Auth::user()->restricted_user !== 1 && Auth::user()->broker !== 'Other')
                        <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.client.telegram-groups.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.client.telegram-groups.*')">
                            {{ __('Groups') }}
                        </x-nav-link>
                        <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.client.referrals.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.client.referrals.*')">
                            {{ __('Referral Program') }}
                        </x-nav-link>
                        <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.client.simulation')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.client.simulation')">
                            {{ __('Calculate your potential profits') }}
                        </x-nav-link>
                        <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.client.top-rank')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.client.top-rank')">
                            {{ __('dashboard.top-ranks') }}
                        </x-nav-link>
                    @endif
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.client.courses.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.client.courses.*')">
                        {{ __('Courses') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.client.users.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.client.users*')">
                        {{ __('My profile') }}
                    </x-nav-link>
                @endif
            </ul>

        </div>
        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-x-hidden overflow-y-auto">
            <div class="md:static mobile-header">
                @include('layouts.navigation')
                <!-- Header -->
                <header class="shadow flex justify-between items-start py-3 border-b-4 border-gray-600 rounded-tl-lg">
                    <div class="py-2 px-2 md:px-6 text-gray-100 font-bold">
                        {{ $header ?? '' }}
                    </div>
                </header>
            </div>

            @if (session('success'))
                <x-success>
                    {{ session('success') }}
                </x-success>
            @endif
            @if (session('error'))
                <x-error>
                    {{ session('error') }}
                </x-error>
            @endif
            @isset($error)
                <x-error>
                    {{ $error }}
                </x-error>
            @endisset

            <!-- Page content -->
            <main class="flex-1 bg-black-300 pt-[160px] md:pt-0 pb-[60px] md:pb-0">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
            
            @if (!auth()->guard('admin')->check())
                <!-- Updated footer with more discreet design -->
                <footer class="border-t border-gray-800 bg-[#12181F] text-gray-500 pt-4 px-4 pb-[120px] md:pb-4 mt-auto">
                    <div class="max-w-7xl mx-auto text-center text-xs">
                        <p class="text-[12px] md:text-[14px] leading-relaxed text-white opacity-75 hover:opacity-100 transition-opacity duration-200">
                            {{ __('This website is for educational and illustrative purposes only. No information presented here constitutes an investment recommendation or guarantees future results. Trading in the financial markets involves risks, including the total loss of invested capital. The automated trading system executes predefined strategies set by the user, who maintains full control over their account and settings.') }}
                        </p>
                    </div>
                </footer>
            @endif
        </div>
    </div>


    @stack('scripts')
</body>

</html>