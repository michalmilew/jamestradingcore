<x-app-layout>
    <x-slot name="header">
        <h2>
            {{ __('Test Notifications') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-6">
        <h2 class="text-normal md:text-2xl font-semibold mb-6">Notification Test Form</h2>

        {{-- Display Success Message --}}
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- Display Error Message --}}
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        {{-- Display Validation Errors --}}
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Tab Navigation --}}
        <div class="tabs mb-6">
            <ul class="flex border-b items-end">
                <li class="mr-1 w-[50%] md:w-[160px] hidden">
                    <a href="#testNotification"
                        class="w-full text-xs md:text-sm tab-link px-2 py-1 md:px-4 md:py-2 inline-block bg-green-600 text-white font-semibold rounded-md h-[40px] items-center flex justify-center"
                        onclick="showTab('testNotification')">
                        Single Test
                    </a>
                </li>
                <li class="mr-1 w-[50%] md:w-[160px]">
                    <a href="#conditionalNotifications"
                        class="w-full text-xs md:text-sm tab-link px-2 py-1 md:px-4 md:py-2 inline-block bg-gray-300 text-gray-800 font-semibold rounded-md h-[40px] items-center flex justify-center"
                        onclick="showTab('conditionalNotifications')">
                        Conditional Test
                    </a>
                </li>
            </ul>
        </div>

        {{-- Conditional Notifications Form --}}
        <div id="conditionalNotifications" class="tab-content hidden">
            <form action="{{ route('admin.notification.conditional.send') }}" method="POST" class="space-y-6">
                @csrf

                <div class="form-group">
                    <label for="name" class="block text-sm font-medium text-white">User Name</label>
                    <input type="text"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        id="name" name="name" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="profit" class="block text-sm font-medium text-white">Profit (PnL)</label>
                    <input type="number" step="0.01"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        id="profit" name="profit" value="{{ old('profit') }}" required>
                </div>

                <div class="form-group">
                    <label for="balance" class="block text-sm font-medium text-white">Balance</label>
                    <input type="number" step="0.01"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        id="balance" name="balance" value="{{ old('balance') }}">
                </div>

                <div class="form-group">
                    <label for="margin" class="block text-sm font-medium text-white">Margin</label>
                    <input type="number" step="0.01"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        id="margin" name="margin" value="{{ old('margin') }}">
                </div>

                <div class="form-group">
                    <label for="risk_level_conditional" class="block text-sm font-medium text-white">Risk/VIP Level</label>
                    <select
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        id="risk_level_conditional" name="risk_level" onchange="syncNotificationType()">
                        <option value="">{{ __('None') }}</option>
                        <option value="Low">{{ __('Low') }}</option>
                        <option value="Medium">{{ __('Medium') }}</option>
                        <option value="High">{{ __('High') }}</option>
                        <option value="Pro">{{ __('Pro') }}</option>
                        <option value="Pro+">{{ __('Pro+') }}</option>
                        <option value="Pro++">{{ __('Pro++') }}</option>
                        <option value="Pro+++">{{ __('Pro+++') }}</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="language_conditional" class="block text-sm font-medium text-white">Language</label>
                    <select
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        id="language_conditional" name="language">
                        <option value="en">English</option>
                        <option value="de">German</option>
                        <option value="es">Spanish</option>
                        <option value="fr">French</option>
                        <option value="it">Italian</option>
                        <option value="nl">Dutch</option>
                        <option value="pt">Portuguese</option>
                    </select>
                </div>

                <button type="submit" name="action" value="send_conditional"
                    class="text-xs md:text-sm px-4 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Submit
                </button>
            </form>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            var tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(function (tab) {
                tab.classList.add('hidden');
            });

            var activeTab = document.getElementById(tabId);
            if (activeTab) {
                activeTab.classList.remove('hidden');
            }

            var tabLinks = document.querySelectorAll('.tab-link');
            tabLinks.forEach(function (link) {
                link.classList.remove('bg-green-600', 'text-white');
                link.classList.add('bg-gray-300', 'text-gray-800');
            });

            var activeLink = document.querySelector(`a[href="#${tabId}"]`);
            if (activeLink) {
                activeLink.classList.add('bg-green-600', 'text-white');
                activeLink.classList.remove('bg-gray-300', 'text-gray-800');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            showTab('conditionalNotifications');
        });
    </script>
</x-app-layout>
