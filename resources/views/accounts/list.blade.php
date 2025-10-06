<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Accounts') }}</h2>
    </x-slot>
    @isset($error)

       <div class="text-red-700"></div> {{ $error }}

    @endif
    <div class="py-5">
        <div class="max-w-7x1 mx-auto px-8">
            <div class=" ">
                <div class="overflow-x-auto justify-between  rounded-lg px-2 py-2">
                    <div class="flex justify-between">
                        <form class="flex-col items-center" method="get" id="sortForm">
                            <div class="flex justify-between mb-2">
                                <input class="mr-2 rounded" type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Account') }}">
                                <select name="sort_by" class="px-4 mr-2 block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="balance_asc" {{ request('sort_by') =='balance_asc'?'selected' : '' }}>{{ __('Balance - Ascending') }}</option>
                                    <option value="balance_desc" {{ request('sort_by') =='balance_desc'?'selected' : '' }}>{{ __('Balance - Descending') }}</option>
                                    <option value="groupid_asc" {{ request('sort_by') =='groupid_asc'?'selected' : '' }}>{{ __('Group - Ascending') }}</option>
                                    <option value="groupid_desc" {{ request('sort_by') =='groupid_desc'?'selected' : '' }}>{{ __('Group - Descending') }}</option>
                                    <option value="state_asc" {{ request('sort_by') =='state_asc'?'selected' : '' }}>{{ __('State - Ascending') }}</option>
                                    <option value="state_desc" {{ request('sort_by') =='state_desc'?'selected' : '' }}>{{ __('State - Descending') }}</option>
                                </select>
                                <button class="flex items-center gap-1 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <path d="M21 21L16.65 16.65"></path>
                                    </svg>
                                    {{ __('Search') }}
                                </button>
                            </div>
                            <div class="flex justify-between mb-2">
                                <div class="flex  ">
                                    <x-label for="groupid" :value="__('Risk Setting')" />
                                    <select name="groupid" id="groupid">
                                        <option value="" >{{ __('Select') }}</option>
                                        <option value="LZZiiLZp" {{ request('groupid') == 'LZZiiLZp' ? 'selected' : '' }}>{{ __('High1') }}</option>
                                        <option value="EVZiiLZp" {{ request('groupid') == 'EVZiiLZp' ? 'selected' : '' }}>{{ __('Low1') }}</option>
                                        <option value="ppKiiLZp" {{ request('groupid') == 'ppKiiLZp' ? 'selected' : '' }}>{{ __('PRO+++') }}</option>
                                        <option value="LJKiiLZp" {{ request('groupid') == 'LJKiiLZp' ? 'selected' : '' }}>{{ __('PRO++') }}</option>
                                        <option value="OJKiiLZp" {{ request('groupid') == 'OJKiiLZp' ? 'selected' : '' }}>{{ __('PRO+') }}</option>
                                        <option value="wVZiiLZp" {{ request('groupid') == 'wVZiiLZp' ? 'selected' : '' }}>{{ __('PRO') }}</option>
                                        <option value="tXciiLZp" {{ request('groupid') == 'tXciiLZp' ? 'selected' : '' }}>{{ __('High') }}</option>
                                        <option value="bXciiLZp" {{ request('groupid') == 'bXciiLZp' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                                        <option value="aXciiLZp" {{ request('groupid') == 'aXciiLZp' ? 'selected' : '' }}>{{ __('Low') }}</option>
                                    </select>
                                </div>
                                <div class="flex ">
                                    <x-label for="state" :value="__('State')" />
                                    <select name="state" id="state">
                                        <option value="" >{{ __('Select') }}</option>
                                        <option value="DISCONNECTED" {{ request('state') == 'DISCONNECTED' ? 'selected' : '' }}>{{ __('DISCONNECTED') }}</option>
                                        <option value="CONNECTED" {{ request('state') == 'CONNECTED' ? 'selected' : '' }}>{{ __('CONNECTED') }}</option>
                                        <option value="NONE" {{ request('state') == 'NONE' ? 'selected' : '' }}>{{ __('NONE') }}</option>
                                    </select>
                                </div>

                            </div>
                            <div class="flex justify-between mb-2">
                                <div class="flex">
                                    <x-label for="min" :value="__('Balance')" />
                                    <x-label for="min" :value="__('From')" />
                                    <input class="mr-2 rounded" type="text" name="min" value="{{ request('min') }}" placeholder="{{ __('min') }}">
                                    <x-label for="max" :value="__('To')" />
                                    <input class="mr-2 rounded" type="text" name="max" value="{{ request('max') }}" placeholder="{{ __('max') }}">

                                </div>
                            </div>
                            <div class="flex justify-between mb-2">
                                <div class="flex">
                                    <x-label for="minlots" :value="__('Lots')" />
                                    <x-label for="minlots" :value="__('From')" />
                                    <input class="mr-2 rounded" type="text" name="minlots" value="{{ request('minlots') }}" placeholder="{{ __('min') }}">
                                    <x-label for="maxlots" :value="__('To')" />
                                    <input class="mr-2 rounded" type="text" name="maxlots" value="{{ request('maxlots') }}" placeholder="{{ __('max') }}">

                                </div>
                            </div>
                        </form>
                        <a href="{{route(\App\Models\SettingLocal::getLang().'.admin.accounts.create') }}" class="flex items-center gap-1 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 h-[42.78px]">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            {{ __('Add Account') }}
                        </a>
                    </div>

                </div>
                <div class="overflow-x-auto  rounded-lg px-2 mt-2">
                    <table class="table-auto w-full mb-2">
                        <thead>
                            <tr class="border-b border-indigo-400">
                                <th class="px-4 py-2 text-left">{{ __('Account ID') }}</th>
                                <th class="px-4 py-2 text-left">{{ __('Name') }}</th>
                                <th class="px-4 py-2 text-left">{{ __('Account') }}</th>
                                <th class="px-4 py-2 text-left">{{ __('Lots') }}</th>
                                <th class="px-4 py-2 text-left">{{ __('Broker') }}</th>
                                <th class="px-4 py-2 text-left">{{ __('Balance') }}</th>
                                <th class="px-4 py-2 text-left">{{ __('Risk Setting') }}</th>
                                <th class="px-4 py-2 text-left">{{ __('State') }}</th>
                                <th class="px-4 py-2 text-left">{{ __('Status') }}</th>
                                <th class="px-4 py-2 text-left">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts as $account)
                            <tr class="border-b border-indigo-400 ">
                                <td class="py-2 px-4"><div class="hidden">{{$account}}</div>{{$account->login}}</td>
                                <td class="py-2 px-4 text-left">{{$account->name}}</td>
                                <td class="py-2 px-4 text-left">{{$account->account}}</td>
                                <td class="py-2 px-4 text-left">{{$account->getClosedPosition()}}</td>
                                <td class="py-2 px-4 text-left">{{$account->broker}}</td>
                                <td class="py-2 px-4 text-left" id="balance_{{$account->account_id}}">{{$account->balance}}</td>
                                <td class="py-2 px-4 text-left" >{{ __(App\Models\TradingGroup::groupName($account->groupid))}}</td>
                                <td class="py-2 px-4 text-left" id="state_{{$account->account_id}}">{{ __($account->state)}}</td>
                                <td class="py-2 px-4 text-left" id="status_{{$account->account_id}}">{{ __($account->status)}}</td>
                                <td class="flex py-2 px-4">

                                    <a href="{{ route(\App\Models\SettingLocal::getLang().'.admin.accounts.show', $account->account_id) }}" class="bg-blue-500 hover:bg-blue-700 text-gray font-bold py-2 px-4 rounded mx-2">View</a>

                                    <a href="{{ route(\App\Models\SettingLocal::getLang().'.admin.accounts.edit', $account->account_id) }}" class="bg-green-500 hover:bg-green-700 text-gray font-bold py-2 px-4 rounded mx-2">{{ __('Edit') }}</a>

                                    <form action="{{ route(\App\Models\SettingLocal::getLang().'.admin.accounts.destroy', $account->account_id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm({{ __('Are you sure you want to delete this Account?') }})" class="bg-red-500 hover:bg-red-700 text-gray font-bold py-2 px-4 rounded">{{ __('Delete') }}</button>
                                    </form>

                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$accounts->links()}}
                    <div class="py-4">

                    </div>

                </div>
            </div>

        </div>
    </div>
    <script>
        function refreshData() {
            var accountIds = @json($accounts->pluck('account_id')->toArray());
            var langValue = @json(\App\Models\SettingLocal::getLang());

            accountIds.forEach(function(id) {
                document.getElementById('status_' + id).innerHTML = '<svg class="animate-spin h-5 w-5 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>';
            });
            setTimeout(function() {}, 1000);
            // Get the CSRF token value from the hidden input field
            var csrfToken = document.querySelector('input[name="_token"]').value;
            //var csrfToken = $('meta[name="csrf-token"]').attr('content'); // Get CSRF token value
            $.ajax({
                url: '/'+langValue+'/admin/accounts/refreshindex',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: csrfToken,
                    account_ids: accountIds
                },
                success: function(response) {
                    console.log(response.data);
                    response.data.forEach(function(account) {
                        //console.log('status_' +account.account_id);
                        document.getElementById('status_' + account.account_id).innerHTML = account.status;
                        document.getElementById('state_' + account.account_id).innerHTML = account.state;
                        document.getElementById('balance_' + account.account_id).innerHTML = account.balance;
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        // Call the named function when needed
        $(document).ready(function() {
            setInterval(refreshData, 10000); // Call every 5 seconds
        });
    </script>
</x-app-layout>
