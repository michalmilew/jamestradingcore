<x-app-layout>
    <x-slot name="header">
        <h2 class="semibold text-xl">
            {{ __('Users') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7x1 mx-auto px-8">
            <div class=" ">
                @isset($error)
                    <x-error>
                        {{ $error }}
                    </x-error>
                    @endif
                    <div class="overflow-x-auto justify-between rounded-lg px-2 py-2">
                        <div class="flex justify-between flex-col md:flex-row gap-4">
                            <form class="flex-col items-center" method="get">
                                <div class="flex justify-between gap-4 flex-col md:flex-row">
                                    <div class="flex-col items-center">
                                        <div class="flex mb-2">
                                            <x-label for="account" :value="__('Search')" class="flex items-center w-[80px]" />
                                            <input class="mr-2 rounded w-full" type="text" name="search"
                                                value="{{ request('search') }}" placeholder="Search...">
                                        </div>
                                        <div class="flex mb-2">
                                            <x-label for="account" :value="__('Sort')" class="flex items-center w-[80px]" />
                                            <select name="sort_by" onchange="document.getElementById('sortForm').submit();"
                                                class="px-4 mr-2 block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                                <option value="user_accounts_count-asc">{{ __('Account - Ascending') }}
                                                </option>
                                                <option value="user_accounts_count-desc">{{ __('Account - Descending') }}
                                                </option>
                                                <option value="paid-asc">{{ __('Paid - Ascending') }}</option>
                                                <option value="paid-desc">{{ __('Paid - Descending') }}</option>
                                                <option value="lots-asc">{{ __('Lots - Ascending') }}</option>
                                                <option value="lots-desc">{{ __('Lots - Descending') }}</option>
                                            </select>
                                        </div>
                                        <div class="flex justify-between gap-4 flex-col md:flex-row">
                                            <div class="flex items-center">
                                                <x-label for="is_vip" :value="__('PRO')"
                                                    class="flex items-center w-[80px]" />
                                                <select
                                                    class="px-4 mr-2 block mt-1 border-gray-300 rounded-md shadow-sm w-[150px]"
                                                    name="is_vip">
                                                    <option value=""></option>
                                                    <option value="0" {{ request('is_vip') == '0' ? 'selected' : '' }}>
                                                        {{ __('No') }}</option>
                                                    <option value="1" {{ request('is_vip') == 1 ? 'selected' : '' }}>
                                                        {{ __('PRO') }}</option>
                                                    <option value="2" {{ request('is_vip') == 2 ? 'selected' : '' }}>
                                                        {{ __('PRO+') }}</option>
                                                    <option value="3" {{ request('is_vip') == 3 ? 'selected' : '' }}>
                                                        {{ __('PRO++') }}</option>
                                                    <option value="4" {{ request('is_vip') == 4 ? 'selected' : '' }}>
                                                        {{ __('PRO+++') }}</option>
                                                </select>
                                            </div>
                                            <div class="flex items-center">
                                                <x-label for="account" :value="__('Account')"
                                                    class="flex items-center w-[80px]" />
                                                <select id="account"
                                                    class="px-4 mr-2 block mt-1 border-gray-300 rounded-md shadow-sm w-[150px]"
                                                    name="account">
                                                    <option value=""></option>
                                                    <option value="Yes"
                                                        {{ request('account') == 'Yes' ? 'selected' : '' }}>
                                                        {{ __('Yes') }}</option>
                                                    <option value="No"
                                                        {{ request('account') == 'No' ? 'selected' : '' }}>
                                                        {{ __('No') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="flex justify-between mb-2 gap-4 flex-col md:flex-row">
                                            <div class="flex items-center">
                                                <x-label for="paid" :value="__('Paid')"
                                                    class="flex items-center w-[80px]" />
                                                <select id="paid"
                                                    class="px-4 mr-2 block mt-1 border-gray-300 rounded-md shadow-sm w-[150px]"
                                                    name="paid">
                                                    <option value=""></option>
                                                    <option value="Yes"
                                                        {{ request('paid') == 'Yes' ? 'selected' : '' }}>
                                                        {{ __('Yes') }}</option>
                                                    <option value="No" {{ request('paid') == 'No' ? 'selected' : '' }}>
                                                        {{ __('No') }}</option>
                                                </select>
                                            </div>
                                            <div class="flex items-center">
                                                <x-label for="lang" :value="__('Language')"
                                                    class="flex items-center w-[80px]" />
                                                <select name="lang" id="lang"
                                                    class="px-4 mr-2 block mt-1 border-gray-300 rounded-md shadow-sm w-[150px]">
                                                    <option value=""></option>
                                                    @foreach (\App\Models\SettingLocal::getLangs() as $key => $value)
                                                        <option value="{{ $key }}"
                                                            {{ request('lang') == $key ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>
                                        <div class="flex justify-between mb-2 gap-4 flex-col md:flex-row">
                                            <div class="flex items-center">
                                                <x-label for="minlots" :value="__('Lots')"
                                                    class="flex items-center w-[80px]" />
                                                <input class="mr-2 w-[150px]" type="text" name="minlots"
                                                    value="{{ request('minlots') }}" placeholder="{{ __('min') }}">
                                            </div>
                                            <div class="flex items-center">
                                                <x-label for="maxlots" :value="__('To')"
                                                    class="flex items-center w-[80px]" />
                                                <input class="mr-2 w-[150px]" type="text" name="maxlots"
                                                    value="{{ request('maxlots') }}" placeholder="{{ __('max') }}">

                                            </div>
                                        </div>
                                        <div class="flex justify-between mb-2 gap-4 flex-col md:flex-row">
                                            <div class="flex items-center">
                                                <x-label for="broker" :value="__('Broker')"
                                                    class="flex items-center w-[80px]" />
                                                <select id="broker"
                                                    class="px-4 mr-2 block mt-1 border-gray-300 rounded-md shadow-sm w-[150px]"
                                                    name="broker">
                                                    <option value=""></option>
                                                    <option value="IronFX"
                                                        {{ request('broker') == 'IronFX' ? 'selected' : '' }}>
                                                        IronFX</option>
                                                    <option value="T4Trade"
                                                        {{ request('broker') == 'T4Trade' ? 'selected' : '' }}>
                                                        T4Trade</option>
                                                    <option value="Other"
                                                        {{ request('broker') == 'Other' ? 'selected' : '' }}>
                                                        Other</option>
                                                </select>
                                            </div>
                                            <div class="flex items-center">
                                                <x-label for="idbroker" :value="__('ID Broker')"
                                                    class="flex items-center w-[80px]" />
                                                <input class="mr-2 w-[150px]" type="text" name="idbroker"
                                                    value="{{ request('idbroker') }}"
                                                    placeholder="{{ __('ID Broker') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <button
                                        class="flex items-center gap-1 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 h-[46px]">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="w-5 h-5">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <path d="M21 21L16.65 16.65"></path>
                                        </svg>
                                        {{ __('Search') }}
                                    </button>
                                </div>
                            </form>

                            <a href="{{ route(\App\Models\SettingLocal::getLang() . '.admin.users.create') }}"
                                class="flex items-center gap-1 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 h-[42.78px]">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="w-5 h-5">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="16"></line>
                                    <line x1="8" y1="12" x2="16" y2="12"></line>
                                </svg>
                                {{ __('Add') }}
                            </a>
                        </div>

                    </div>
                    <div class="overflow-x-auto rounded-lg px-2 mt-2">
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        <table class="table-auto divide-y divide-gray-200 sm:table-auto">
                            <thead>
                                <tr class="border-b border-indigo-400">
                                    <td class="px-4 text-left">{{ __('Name') }}</td>
                                    <td class="px-4 text-left">{{ __('Email') }}</td>
                                    {{-- <td class="px-4 text-left">{{__('Account')}}</td> --}}
                                    <td class="px-4 text-left" style="width:200px">{{ __('Lots') }}</td>
                                    <td class="px-4 text-left" style="width:200px">{{ __('PRO') }}</td>
                                    <td class="px-4 text-left" style="width:200px">{{ __('Paid') }}</td>
                                    <td class="px-4 text-left" style="width:200px">{{ __('Language') }}</td>
                                    <td class="px-4 text-left" style="width:200px">{{ __('FTD') }}</td>
                                    <td class="px-4 text-left" style="width:200px">{{ __('ID Broker') }}</td>
                                    <td class="px-4 text-left" style="width:200px">{{ __('IG User') }}</td>
                                    <td class="px-4 text-left" style="width:200px">{{ __('Broker') }}</td>
                                    <td class="px-4">
                                        {{ __('Actions') }}
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr class="border-b">
                                        <td>
                                            <div class="col-span-1 flex p-2">{{ $user->name }}</div>
                                        </td>
                                        <td>
                                            <div class="col-span-1 flex p-2">{{ $user->email }}</div>
                                        </td>
                                        <td>
                                            <div class="col-span-1 flex p-2 text-center">
                                                {{ $user->lots }}
                                            </div>

                                        </td>
                                        <td colspan="7">
                                            <form
                                                action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.users.update', $user->id) }}"
                                                method="post" id="update_{{ $user->id }}">
                                                @csrf
                                                <div class="grid grid-cols-7 w-full gap-1  pb-1">
                                                    <div class="col-span-1 flex">
                                                        <select id="is_vip" class="block w-full" name="is_vip">
                                                            <option value=""></option>
                                                            <option value="0"
                                                                {{ $user->is_vip == 0 ? 'selected' : '' }}>
                                                                {{ __('No') }}</option>
                                                            <option value="1"
                                                                {{ $user->is_vip == 1 ? 'selected' : '' }}>
                                                                {{ __('PRO') }}</option>
                                                            <option value="2"
                                                                {{ $user->is_vip == 2 ? 'selected' : '' }}>
                                                                {{ __('PRO+') }}</option>
                                                            <option value="3"
                                                                {{ $user->is_vip == 3 ? 'selected' : '' }}>
                                                                {{ __('PRO++') }}</option>
                                                            <option value="4"
                                                                {{ $user->is_vip == 4 ? 'selected' : '' }}>
                                                                {{ __('PRO+++') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-span-1 flex">
                                                        <select id="paid" class="block w-full" name="paid">
                                                            <option value=""></option>
                                                            <option value="Yes"
                                                                {{ $user->paid == 'Yes' ? 'selected' : '' }}>
                                                                {{ __('Yes') }}</option>
                                                            <option value="No"
                                                                {{ $user->paid == 'No' ? 'selected' : '' }}>
                                                                {{ __('No') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-span-1 flex">
                                                        <select name="lang" id="lang" class="block w-full px-1">
                                                            <option value=""></option>
                                                            @foreach (\App\Models\SettingLocal::getLangs() as $key => $value)
                                                                <option value="{{ $key }}"
                                                                    {{ $user->lang == $key ? 'selected' : '' }}>
                                                                    {{ $value }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-span-1 flex">
                                                        <x-input id="ftd" class="block mt-1 w-full" type="text"
                                                            name="ftd" value="{{ $user->ftd }}" />
                                                    </div>
                                                    <div class="col-span-1 flex">
                                                        <x-input class="block mt-1 w-full" type="text"
                                                            name="id_broker" value="{{ $user->id_broker }}" required />
                                                    </div>
                                                    <div class="col-span-1 flex">
                                                        <x-input class="block mt-1 w-full" type="text" name="ig_user"
                                                            value="{{ $user->ig_user }}" required />
                                                    </div>
                                                    <div class="col-span-1 flex">
                                                        <select id="broker" class="block mt-1 w-full" name="broker"
                                                            value="{{ $user->broker }}" required>
                                                            <option value=""></option>
                                                            <option value="IronFX"
                                                                {{ $user->broker == 'IronFX' ? 'selected' : '' }}>IronFX
                                                            </option>
                                                            <option value="T4Trade"
                                                                {{ $user->broker == 'T4Trade' ? 'selected' : '' }}>T4Trade
                                                            </option>
                                                            <option value="Other"
                                                                {{ $user->broker == 'Other' ? 'selected' : '' }}>Other
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="col-span-1 flex p-2">
                                                        {{ __('Notes') }}
                                                    </div>
                                                    <div class="col-span-5 flex">
                                                        <x-input class="block mt-1 w-full" type="text" name="notes"
                                                            value="{{ $user->notes }}" />
                                                    </div>
                                                    <div class="col-span-1 flex">
                                                        <x-button class="ml-3 mt-4">
                                                            {{ __('Save') }}
                                                        </x-button>
                                                    </div>
                                                    <!-- Restricted User Checkbox -->
                                                    <div class="col-span-2 flex items-center space-x-2">
                                                        <label class="inline-flex items-center">
                                                            <input type="checkbox" 
                                                                name="restricted_user" 
                                                                value="1"
                                                                class="form-checkbox h-5 w-5 text-purple-600 bg-gray-700 border-gray-600 rounded"
                                                                {{ $user->restricted_user === 1 ? 'checked' : '' }}>
                                                            <span class="ml-2 text-gray-300 font-medium">{{ __('restricted_user') }}</span>
                                                        </label>
                                                    </div>

                                                    <button type="button" 
                                                        class="w-40 p-2.5 rounded-[10px] text-sm text-white bg-[#8a2be2]"
                                                        onclick="showForcedAccessModal({{ $user->id }})"
                                                    >
                                                        {{ __('Force Group Access') }}
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                        <td class="flex py-2 px-4">
                                            <a href="{{ route(\App\Models\SettingLocal::getLang() . '.admin.users.edit', $user) }}"
                                                class="bg-green-500 hover:bg-green-700 text-gray font-bold py-2 px-2 mr-2 rounded h-full text-[smaller]">{{ __('Edit') }}</a>

                                            <form
                                                action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.users.destroy', $user->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Are you sure you want to delete this user?')"
                                                    class="bg-red-500 hover:bg-red-700 text-gray font-bold py-2 px-2 mr-2 rounded text-[smaller]">{{ __('Delete') }}</button>
                                            </form>
                                            <form
                                                action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.users.sendresetpasswordlink', $user->id) }}"
                                                method="POST" class="inline w-[88px]">
                                                @csrf
                                                <button type="submit"
                                                    onclick="return confirm('Are you sure, you want to send a reset password link?')"
                                                    class="bg-blue-500 hover:bg-blue-700 text-gray font-bold py-2 px-2 rounded text-[smaller]">{{ __('Reset PWD') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="py-4">
                            {{ $users->appends(request()->input())->links() }}
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Update the modal HTML with dark theme styling -->
    <div id="forcedAccessModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md" style="background-color: #12181F;">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-200">{{ __('Force Telegram Group Access') }}</h3>
                <div class="mt-2 space-y-3">
                    @foreach($telegramGroups as $group)
                    <label class="inline-flex items-center">
                        <input type="checkbox" 
                            name="forced_groups[]" 
                            value="{{ $group->id }}"
                            class="form-checkbox h-4 w-4 text-purple-600 bg-gray-700 border-gray-600 rounded forced-group-checkbox">
                        <span class="ml-2 text-gray-300">{{ $group->name }} (Min: ${{ number_format($group->min_balance, 2) }})</span>
                    </label>
                    @endforeach
                </div>
                <div class="mt-4 flex justify-end space-x-3">
                    <button type="button" 
                        class="px-4 py-2 bg-gray-700 text-gray-300 rounded-md hover:bg-gray-600"
                        onclick="closeForcedAccessModal()">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700"
                        onclick="saveForcedAccess()">
                        {{ __('Save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add loading overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden items-center justify-center z-50">
        <div class="text-center">
            <div class="inline-block">
                <div class="w-12 h-12 border-4 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
            <p class="mt-2 text-gray-200">{{ __('Updating access...') }}</p>
        </div>
    </div>

    @push('scripts')
    <script>
    const currentLang = '{{ \App\Models\SettingLocal::getLang() }}';
    let currentUserId = null;

    function showLoading() {
        document.getElementById('loadingOverlay').classList.remove('hidden');
        document.getElementById('loadingOverlay').classList.add('flex');
    }

    function hideLoading() {
        document.getElementById('loadingOverlay').classList.remove('flex');
        document.getElementById('loadingOverlay').classList.add('hidden');
    }

    function showForcedAccessModal(userId) {
        currentUserId = userId;
        const modal = document.getElementById('forcedAccessModal');
        modal.classList.remove('hidden');
        
        // Clear all checkboxes
        document.querySelectorAll('.forced-group-checkbox').forEach(cb => cb.checked = false);
        
        // Get current forced groups and check the appropriate boxes
        const user = @json($users->keyBy('id'));
        const forcedGroups = user[userId].forced_telegram_groups || [];
        
        forcedGroups.forEach(group => {
            const checkbox = document.querySelector(`input[name="forced_groups[]"][value="${group.id}"]`);
            if (checkbox) checkbox.checked = true;
        });
    }

    function closeForcedAccessModal() {
        document.getElementById('forcedAccessModal').classList.add('hidden');
        currentUserId = null;
    }

    function saveForcedAccess() {
        if (!currentUserId) return;
        
        showLoading();
        const checkedGroups = Array.from(document.querySelectorAll('.forced-group-checkbox:checked'))
            .map(cb => cb.value);
        
        fetch(`/${currentLang}/admin/users/${currentUserId}/forced-access`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ forced_groups: checkedGroups })
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Failed to update forced access');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            alert('Failed to update forced access');
        });
    }
    </script>
    @endpush

</x-app-layout>
