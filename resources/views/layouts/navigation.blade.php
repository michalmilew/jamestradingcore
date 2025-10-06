<nav x-data="{ open: false }" class="text-gray-400" style="background-color:#12181F">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto px-4 px-6 md:px-8 md:py-3">
        <div class="flex justify-between sm:h-10">
            <div class="flex">
                <!-- Navigation Links -->
                <div class="block items-center space-x-8 sm:-my-px sm:hidden">
                    @if (auth()->guard('admin')->check())
                        <a href="{{ route(\App\Models\SettingLocal::getLang() . '.admin.dashboard') }}">
                            <x-application-logo class="block w-32 fill-current text-gray-600" />
                        </a>
                    @else
                        <a href="{{ route(\App\Models\SettingLocal::getLang() . '.client.accounts.index') }}">
                            <x-application-logo class="block w-32 fill-current text-gray-600" />
                        </a>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6 gap-6">
                <div class="flex items-center text-black">
                    @foreach (\App\Models\SettingLocal::getLangs() as $key => $value)
                        <a class="mr-2" style="height:35px" href="/lang/{{ $key }}">
                            <img src="{{ asset('images/' . $key . '.svg') }}" alt="{{ $value }}">
                        </a>
                    @endforeach
                </div>
                <div class="h-16 flex items-center p-5 relative" style="background-color:#282e37">
                    <button id="dropdownButton"
                        class="flex items-center text-sm font-medium text-gray-400 hover:text-gray-300 transition duration-150 ease-in-out">
                        <div>{{ Auth::user()->name }}</div>
                        <div class="ml-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                    <div id="dropdownMenu"
                        class="dropdown-menu absolute right-0 mt-[105px] w-[188px] bg-[#3A4047] text-white hidden">
                        <form method="POST"
                            action="{{ request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.*') ? route(\App\Models\SettingLocal::getLang() . '.admin.logout') : route(\App\Models\SettingLocal::getLang() . '.logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left hover:bg-grey-600">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }"
        class="fixed right-0 z-50 bg-gradient-to-r from-gray-800 to-gray-600 pt-5 pb-[100px] px-5 sm:hidden overflow-y-scroll h-[calc(100vh_-_52px)] top-[82px] w-full">
        <div class="pt-2 pb-3 space-y-1">
            <ul class="flex flex-col">
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
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.metatrader.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.metatrader.*')">
                        {{ __('MetaTrader Management') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.servers.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.servers*')">
                        {{ __('Servers') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.courses.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.courses.*')">
                        {{ __('Courses') }}
                    </x-nav-link>
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.pnl.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.pnl.*')">
                        {{ __('Pnl') }}
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
                    <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.affiliates.index', ['language' => \App\Models\SettingLocal::getLang()])" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.affiliates.*')">
                        {{ __('Affiliates') }}
                    </x-nav-link>
                    <!--

                <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.subscriptions.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.subscriptions*')">
                    {{ __('Subscriptions') }}
                </x-nav-link>
                <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.settings.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.settings.index')">
                    {{ __('Slave/Group Settings') }}
                </x-nav-link>
                <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.settings.symbols')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.settings.symbols')">
                    {{ __('Symbol Settings') }}
                </x-nav-link>
                <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.settings.protections')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.settings.protections')">
                    {{ __('Global Protection') }}
                </x-nav-link>
                <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.groups.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.groups*')">
                    {{ __('Groups') }}
                </x-nav-link>
                <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.positions.open')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.positions*')">
                    {{ __('Positions') }}
                </x-nav-link>
                <x-nav-link :href="route(\App\Models\SettingLocal::getLang() . '.admin.orders.index')" :active="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.orders*')">
                    {{ __('Orders') }}
                </x-nav-link>
                -->
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

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1">
            <div class="px-4">
                <div class="font-medium text-base text-gray-300">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-300">{{ Auth::user()->email }}</div>
            </div>
            <div class="flex items-center mt-4">
                @foreach (\App\Models\SettingLocal::getLangs() as $key => $value)
                    <a class="mr-2" style="height:35px" href="/{{ $key }}">
                        <img src="{{ asset('images/' . $key . '.svg') }}" alt="{{ $value }}">
                    </a>
                @endforeach
            </div>
            <div class="mt-3 space-y-1 text-gray-300">
                <!-- Authentication -->
                <form method="POST"
                    action="{{ request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.*') ? route(\App\Models\SettingLocal::getLang() . '.admin.logout') : route(\App\Models\SettingLocal::getLang() . '.logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="request()->routeIs(\App\Models\SettingLocal::getLang() . '.admin.*')
                        ? route(\App\Models\SettingLocal::getLang() . '.admin.logout')
                        : route(\App\Models\SettingLocal::getLang() . '.logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.getElementById('dropdownButton');
        const menu = document.getElementById('dropdownMenu');
        menu.classList.add('hidden');

        button.addEventListener('click', function() {
            menu.classList.toggle('hidden');
        });

        // Optional: Close the menu if clicking outside of it
        document.addEventListener('click', function(event) {
            if (!button.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    });
</script>
