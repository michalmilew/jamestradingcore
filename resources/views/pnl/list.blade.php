<x-app-layout>
    <x-slot name="header">
        <h2 class="semibold text-xl">
            {{ __('dashboard.pnl-title') }}
        </h2>
    </x-slot>
    <div class="py-6">
        <div class="mx-auto sm:px-6 ">
            <div class="overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">

                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 ">
                                <div class="overflow-x-auto justify-between rounded-lg px-2 py-2">
                                    <div class="flex justify-between">
                                        <form class="flex-col items-center" method="get" id="sortForm">
                                            <div class="flex justify-between mb-2">
                                                <input class="mr-2 rounded" type="text" name="login" value="{{ request('login') }}" placeholder="{{ __('Account') }}">
                                                <select name="sort_by" class="px-4 mr-2 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                    <option value="pnl_asc" {{ request('sort_by') == 'pnl_asc' ? 'selected' : '' }}>{{ __('Pnl - Ascending') }}</option>
                                                    <option value="pnl_desc" {{ request('sort_by') == 'pnl_desc' ? 'selected' : '' }}>{{ __('Pnl - Descending') }}</option>
                                                </select>
                                                <button class="flex items-center gap-1 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                                        <circle cx="11" cy="11" r="8"></circle>
                                                        <path d="M21 21L16.65 16.65"></path>
                                                    </svg>
                                                    {{ __('Search') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="shadow overflow-hidden sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">{{ __('Account') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">{{ __('Email') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">{{ __('Pnl') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y">
                                            @foreach ($reports as $report)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    {{ $report['login'] ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    {{ $report['name'] ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    €{{ $report['pnl'] ?? 'N/A' }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="py-4">
                                        {{ $reports->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
