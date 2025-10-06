@extends('layouts.app')

@section('content')
<div class="py-5">
    <div class="max-w-7xl mx-auto px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route(\App\Models\SettingLocal::getLang() . '.admin.metatrader.show', $account->id) }}" 
                           class="text-gray-400 hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-300 mb-2">Activity Logs</h1>
                            <p class="text-gray-400">MetaTrader Account #{{ $account->login }}</p>
                        </div>
                    </div>
                </div>
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ (bool)$account->is_connected ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ (bool)$account->is_connected ? 'Connected' : 'Disconnected' }}
                </span>
            </div>
        </div>

        <!-- Account Summary -->
        <div class="bg-dark-2 rounded-lg shadow border border-gray-700 mb-8">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Account</label>
                        <p class="text-white text-sm font-mono">{{ $account->login }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">User</label>
                        <p class="text-white text-sm">{{ $account->user ? $account->user->name : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Server</label>
                        <p class="text-white text-sm">{{ $account->server ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Total Activities</label>
                        <p class="text-white text-sm font-semibold">{{ $logs->total() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Logs -->
        <div class="bg-dark-2 rounded-lg shadow border border-gray-700">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-white mb-6">Activity History</h2>
                
                @if($logs->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Activity</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Details</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">User</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date & Time</th>
                                </tr>
                            </thead>
                            <tbody class="bg-dark-2 divide-y divide-gray-700">
                                @foreach($logs as $log)
                                    <tr class="hover:bg-gray-800">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 rounded-full mr-3 
                                                    {{ $log->activity_type === 'connected' ? 'bg-green-500' : 
                                                       ($log->activity_type === 'disconnected' ? 'bg-red-500' : 
                                                       ($log->activity_type === 'created' ? 'bg-blue-500' : 'bg-gray-500')) }}">
                                                </div>
                                                <span class="text-sm font-medium text-white capitalize">
                                                    {{ str_replace('_', ' ', $log->activity_type) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm text-gray-300">
                                                @if(isset($log->details['action']))
                                                    <div class="font-medium">{{ $log->details['action'] }}</div>
                                                @endif
                                                @if(isset($log->details['admin_id']))
                                                    <div class="text-xs text-gray-400">Admin ID: {{ $log->details['admin_id'] }}</div>
                                                @endif
                                                @if(isset($log->details['old_config']))
                                                    <div class="text-xs text-gray-400">
                                                        <span class="text-red-400">Old:</span> {{ $log->details['old_config'] }}
                                                    </div>
                                                @endif
                                                @if(isset($log->details['new_config']))
                                                    <div class="text-xs text-gray-400">
                                                        <span class="text-green-400">New:</span> {{ $log->details['new_config'] }}
                                                    </div>
                                                @endif
                                                @if(isset($log->details['balance']))
                                                    <div class="text-xs text-gray-400">
                                                        Balance: €{{ number_format($log->details['balance'], 2) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @if($log->user)
                                                <div class="text-sm text-white">{{ $log->user->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $log->user->email }}</div>
                                            @else
                                                <span class="text-gray-500 text-sm">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-300">
                                            <div>{{ $log->created_at->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($logs->hasPages())
                        <div class="bg-gray-800 px-4 py-3 border-t border-gray-700 sm:px-6 mt-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 flex justify-between sm:hidden">
                                    @if($logs->onFirstPage())
                                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-400 bg-gray-700 cursor-not-allowed">
                                            Previous
                                        </span>
                                    @else
                                        <a href="{{ $logs->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-700 hover:bg-gray-600">
                                            Previous
                                        </a>
                                    @endif

                                    @if($logs->hasMorePages())
                                        <a href="{{ $logs->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-700 hover:bg-gray-600">
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
                                            <span class="font-medium text-gray-300">{{ $logs->firstItem() }}</span>
                                            to
                                            <span class="font-medium text-gray-300">{{ $logs->lastItem() }}</span>
                                            of
                                            <span class="font-medium text-gray-300">{{ $logs->total() }}</span>
                                            results
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                            @if($logs->onFirstPage())
                                                <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-600 bg-gray-700 text-sm font-medium text-gray-400 cursor-not-allowed">
                                                    <span class="sr-only">Previous</span>
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            @else
                                                <a href="{{ $logs->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-600 bg-gray-700 text-sm font-medium text-gray-300 hover:bg-gray-600">
                                                    <span class="sr-only">Previous</span>
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                            @endif

                                            @foreach($logs->getUrlRange(1, $logs->lastPage()) as $page => $url)
                                                @if($page == $logs->currentPage())
                                                    <span class="relative inline-flex items-center px-4 py-2 border border-blue-500 bg-blue-600 text-sm font-medium text-white">
                                                        {{ $page }}
                                                    </span>
                                                @else
                                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-600 bg-gray-700 text-sm font-medium text-gray-300 hover:bg-gray-600">
                                                        {{ $page }}
                                                    </a>
                                                @endif
                                            @endforeach

                                            @if($logs->hasMorePages())
                                                <a href="{{ $logs->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-600 bg-gray-700 text-sm font-medium text-gray-300 hover:bg-gray-600">
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
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-300">No activity logs</h3>
                        <p class="mt-1 text-sm text-gray-400">No activity has been recorded for this account yet.</p>
                    </div>
                @endif
            </div>
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