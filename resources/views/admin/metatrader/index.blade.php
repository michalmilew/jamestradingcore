@extends('layouts.app')

@section('content')
<div class="py-5">
    <div class="max-w-8xl mx-auto px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-300 mb-2">MetaTrader Management</h1>
            <p class="text-gray-400">Manage all MT4/MT5 accounts, monitor status, and control trading operations</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-900/50 to-blue-800/30 rounded-xl shadow-lg p-6 border border-blue-700/50 backdrop-blur-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-300 mb-1">Total Accounts</p>
                        <p class="text-3xl font-bold text-blue-100">{{ $stats['total_accounts'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-600/20 text-blue-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-900/50 to-green-800/30 rounded-xl shadow-lg p-6 border border-green-700/50 backdrop-blur-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-300 mb-1">Connected</p>
                        <p class="text-3xl font-bold text-green-100">{{ $stats['connected_accounts'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-600/20 text-green-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-900/50 to-red-800/30 rounded-xl shadow-lg p-6 border border-red-700/50 backdrop-blur-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-300 mb-1">Disconnected</p>
                        <p class="text-3xl font-bold text-red-100">{{ $stats['disconnected_accounts'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-red-600/20 text-red-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-900/50 to-purple-800/30 rounded-xl shadow-lg p-6 border border-purple-700/50 backdrop-blur-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-300 mb-1">MT4/MT5</p>
                        <p class="text-3xl font-bold text-purple-100">{{ $stats['mt4_accounts'] }}/{{ $stats['mt5_accounts'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-purple-600/20 text-purple-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-dark-2 rounded-lg shadow mb-6 border border-gray-700">
            <div class="p-6">
                <form method="GET" action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.metatrader.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-md text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Login, User, Email...">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-md text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="connected" {{ request('status') === 'connected' ? 'selected' : '' }}>Connected</option>
                            <option value="disconnected" {{ request('status') === 'disconnected' ? 'selected' : '' }}>Disconnected</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Platform</label>
                        <select name="platform" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-md text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Platforms</option>
                            @foreach($platforms as $platform)
                                <option value="{{ $platform }}" {{ request('platform') === $platform ? 'selected' : '' }}>{{ $platform }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Server</label>
                        <select name="server" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-md text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Servers</option>
                            @foreach($servers as $server)
                                <option value="{{ $server }}" {{ request('server') === $server ? 'selected' : '' }}>{{ $server }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Accounts Table -->
        <div class="bg-dark-2 rounded-lg shadow overflow-hidden border border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Account</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Server</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Platform</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Equity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Last Activity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
                    <tbody class="bg-dark-2 divide-y divide-gray-700">
                        @forelse($accounts as $account)
                            <tr class="hover:bg-gray-800">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-200">{{ $account->login }}</div>
                                    <div class="text-sm text-gray-400">{{ $account->account_id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($account->user)
                                        <div class="text-sm font-medium text-gray-200">{{ $account->user->name }}</div>
                                        <div class="text-sm text-gray-400">{{ $account->user->email }}</div>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                    {{ $account->server ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $account->platform_type === 'MT4' ? 'bg-purple-900 text-purple-200' : 'bg-orange-900 text-orange-200' }}">
                                        {{ $account->platform_type ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $account->is_connected ? 'bg-green-900 text-green-200' : 'bg-red-900 text-red-200' }}">
                                        {{ $account->is_connected ? 'Connected' : 'Disconnected' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                    {{ $account->balance ? '€' . number_format($account->balance, 2) : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                    {{ $account->equity ? '€' . number_format($account->equity, 2) : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    @if($account->accountActivity && $account->accountActivity->count() > 0)
                                        {{ $account->accountActivity->first()->created_at->diffForHumans() }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route(\App\Models\SettingLocal::getLang() . '.admin.metatrader.show', $account->id) }}" 
                                           class="text-blue-400 hover:text-blue-300">View</a>
                                        
                                        @if($account->is_connected)
                                            <form method="POST" action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.metatrader.disconnect', $account->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Are you sure you want to disconnect this account?')" 
                                                        class="text-red-400 hover:text-red-300">Disconnect</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.metatrader.connect', $account->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-400 hover:text-green-300">Connect</button>
                                            </form>
                                        @endif
                                        
                                        <a href="{{ route(\App\Models\SettingLocal::getLang() . '.admin.metatrader.logs', $account->id) }}" 
                                           class="text-gray-400 hover:text-gray-300">Logs</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-gray-400">
                                    No accounts found.
                </td>
            </tr>
                        @endforelse
        </tbody>
    </table>
            </div>
            
            <!-- Custom Pagination -->
            @if($accounts->hasPages())
                <div class="bg-gray-800 px-4 py-3 border-t border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if($accounts->onFirstPage())
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-400 bg-gray-700 cursor-not-allowed">
                                    Previous
                                </span>
                            @else
                                <a href="{{ $accounts->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-700 hover:bg-gray-600">
                                    Previous
                                </a>
                            @endif

                            @if($accounts->hasMorePages())
                                <a href="{{ $accounts->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-700 hover:bg-gray-600">
                                    Next
                                </a>
                            @else
                                <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-400 bg-gray-700 cursor-not-allowed">
                                    Next
                                </span>
                            @endif
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-400">
                                    Showing
                                    <span class="font-medium text-gray-300">{{ $accounts->firstItem() }}</span>
                                    to
                                    <span class="font-medium text-gray-300">{{ $accounts->lastItem() }}</span>
                                    of
                                    <span class="font-medium text-gray-300">{{ $accounts->total() }}</span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    @if($accounts->onFirstPage())
                                        <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-600 bg-gray-700 text-sm font-medium text-gray-400 cursor-not-allowed">
                                            <span class="sr-only">Previous</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    @else
                                        <a href="{{ $accounts->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-600 bg-gray-700 text-sm font-medium text-gray-300 hover:bg-gray-600">
                                            <span class="sr-only">Previous</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    @endif

                                    @foreach($accounts->getUrlRange(1, $accounts->lastPage()) as $page => $url)
                                        @if($page == $accounts->currentPage())
                                            <span class="relative inline-flex items-center px-4 py-2 border border-blue-500 bg-blue-600 text-sm font-medium text-white">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-600 bg-gray-700 text-sm font-medium text-gray-300 hover:bg-gray-600">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endforeach

                                    @if($accounts->hasMorePages())
                                        <a href="{{ $accounts->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-600 bg-gray-700 text-sm font-medium text-gray-300 hover:bg-gray-600">
                                            <span class="sr-only">Next</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    @else
                                        <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-600 bg-gray-700 text-sm font-medium text-gray-400 cursor-not-allowed">
                                            <span class="sr-only">Next</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    @endif
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Additional custom styles for better dark theme compatibility */
.bg-dark-2 {
    background-color: #1f2937;
}
</style>
@endsection