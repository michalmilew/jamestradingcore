@extends('layouts.app')

@section('content')
<div class="py-5">
    <div class="max-w-7xl mx-auto px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route(\App\Models\SettingLocal::getLang() . '.admin.metatrader.index') }}" 
                           class="text-gray-400 hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-300 mb-2">Account Details</h1>
                            <p class="text-gray-400">MetaTrader Account #{{ $account->login }}</p>
                        </div>
                    </div>
                </div>
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ (bool)$account->is_connected ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ (bool)$account->is_connected ? 'Connected' : 'Disconnected' }}
                </span>
            </div>
        </div>

        <!-- Account Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Account Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Basic Information -->
                <div class="bg-dark-2 rounded-lg shadow border border-gray-700">
                    <div class="p-8">
                        <h2 class="text-lg font-semibold text-white mb-4">Account Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Account Login</label>
                                <p class="text-white font-mono text-sm">{{ $account->login }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Account ID</label>
                                <p class="text-white text-sm">{{ $account->account_id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Server</label>
                                <p class="text-white text-sm">{{ $account->server ?? 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Platform</label>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $account->platform_type === 'MT4' ? 'bg-purple-900 text-purple-200' : 'bg-orange-900 text-orange-200' }}">
                                    {{ $account->platform_type ?? 'Unknown' }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Current Balance</label>
                                <p class="text-white text-base font-semibold">€{{ number_format($account->balance, 2) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Current Equity</label>
                                <p class="text-white text-base font-semibold">€{{ number_format($account->equity, 2) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Risk Setting</label>
                                <p class="text-white text-sm">{{ $account->groupid }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Created At</label>
                                <p class="text-white text-sm">{{ $account->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Information -->
                <div class="bg-dark-2 rounded-lg shadow border border-gray-700">
                    <div class="p-8">
                        <h2 class="text-lg font-semibold text-white mb-4">User Information</h2>
                        @if($account->user)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Name</label>
                                    <p class="text-white text-sm">{{ $account->user->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Email</label>
                                    <p class="text-white text-sm">{{ $account->user->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">User ID</label>
                                    <p class="text-white text-sm">{{ $account->user->id }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Registered</label>
                                    <p class="text-white text-sm">{{ $account->user->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-400 text-sm">No user information available</p>
                        @endif
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-dark-2 rounded-lg shadow border border-gray-700">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-white">Recent Activity</h2>
                            <a href="{{ route(\App\Models\SettingLocal::getLang() . '.admin.metatrader.logs', $account->id) }}" 
                               class="text-blue-400 hover:text-blue-300 text-sm">
                                View All Logs
                            </a>
                        </div>
                        @if($account->accountActivity && $account->accountActivity->count() > 0)
                            <div class="space-y-3">
                                @foreach($account->accountActivity->take(10) as $activity)
                                    <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-2 h-2 rounded-full 
                                                {{ $activity->activity_type === 'connected' ? 'bg-green-500' : 
                                                   ($activity->activity_type === 'disconnected' ? 'bg-red-500' : 'bg-blue-500') }}">
                                            </div>
                                            <div>
                                                <p class="text-white text-sm font-medium">
                                                    {{ ucfirst($activity->activity_type) }}
                                                </p>
                                                <p class="text-gray-400 text-xs">
                                                    {{ $activity->created_at->format('M d, Y H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                        @if(isset($activity->details['action']))
                                            <span class="text-gray-400 text-xs">
                                                {{ $activity->details['action'] }}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400 text-sm">No activity recorded</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar Actions -->
            <div class="space-y-8">
                <!-- Quick Actions -->
                <div class="bg-dark-2 rounded-lg shadow border border-gray-700">
                    <div class="p-8">
                        <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            @if((bool)$account->is_connected)
                                <form method="POST" action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.metatrader.disconnect', $account->id) }}">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Are you sure you want to disconnect this account?')" 
                                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Disconnect Account
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.metatrader.connect', $account->id) }}">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Connect Account
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route(\App\Models\SettingLocal::getLang() . '.admin.metatrader.logs', $account->id) }}" 
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-700 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                View Logs
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Account Statistics -->
                <div class="bg-dark-2 rounded-lg shadow border border-gray-700">
                    <div class="p-8">
                        <h3 class="text-lg font-semibold text-white mb-4">Statistics</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-1">
                                <span class="text-gray-400 text-sm">Total Activities</span>
                                <span class="text-white text-sm font-semibold">{{ $account->accountActivity ? $account->accountActivity->count() : 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="text-gray-400 text-sm">Last Activity</span>
                                <span class="text-white text-sm">
                                    @if($account->accountActivity && $account->accountActivity->count() > 0)
                                        {{ $account->accountActivity->first()->created_at->diffForHumans() }}
                                    @else
                                        Never
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="text-gray-400 text-sm">Days Active</span>
                                <span class="text-white text-sm font-semibold">{{ $account->created_at->diffInDays(now()) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
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