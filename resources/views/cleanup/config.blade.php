<x-app-layout>
    <x-slot name="header">
        <h2>
            {{__('Cron Setting')}}
        </h2>
    </x-slot>

    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-semibold mb-6">Cleanup Settings</h2>
    
        {{-- Display Success Message --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
    
        {{-- Display Validation Errors --}}
        @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    
        {{-- Cleanup Settings Form --}}
        <form action="{{ route(\App\Models\SettingLocal::getLang().'.admin.cleanup.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
    
            <div class="form-group">
                <label for="min_balance" class="block text-sm font-medium text-white">Minimum Balance</label>
                <input 
                    type="number" 
                    step="0.01"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                    id="min_balance" 
                    name="min_balance" 
                    value="{{ old('min_balance', $cleanup->min_balance) }}" 
                    required
                >
            </div>
    
            <div class="form-group">
                <label for="max_balance" class="block text-sm font-medium text-white">Maximum Balance</label>
                <input 
                    type="number" 
                    step="0.01" 
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                    id="max_balance" 
                    name="max_balance" 
                    value="{{ old('max_balance', $cleanup->max_balance) }}" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="min_lot_balance" class="block text-sm font-medium text-white">Minimum Lots Balance</label>
                <input 
                    type="number" 
                    step="0.01" 
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                    id="min_lot_balance" 
                    name="min_lot_balance" 
                    value="{{ old('min_lot_balance', $cleanup->min_lot_balance) }}" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="disconnect_limit_time" class="block text-sm font-medium text-white">Disconnect limit time (minutes)</label>
                <input 
                    type="number" 
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                    id="disconnect_limit_time" 
                    name="disconnect_limit_time" 
                    value="{{ old('disconnect_limit_time', $cleanup->disconnect_limit_time) }}" 
                    required
                >
            </div>
    
            <div class="form-group">
                <label for="cleanup_period" class="block text-sm font-medium text-white">Cleanup Period (days)</label>
                <input 
                    type="number" 
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                    id="cleanup_period" 
                    name="cleanup_period" 
                    value="{{ old('cleanup_period', $cleanup->cleanup_period) }}" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="cleanup_time" class="block text-sm font-medium text-white">Cleanup Execution Time (HH:MM)</label>
                <input 
                    type="time"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                    id="cleanup_time" 
                    name="cleanup_time" 
                    value="{{ old('cleanup_time', $cleanup->cleanup_time) }}" 
                    required
                >
            </div>
    
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Update Settings
            </button>
        </form>

        <form action="{{ route(\App\Models\SettingLocal::getLang().'.admin.cleanup.execute') }}" method="POST">
            @csrf
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 relative left-[10rem] bottom-[40px]">
                Cleanup Now
            </button>
        </form>

        <h2 class="text-2xl font-semibold mb-6">Inactive User Settings</h2>
    
        {{-- Cleanup Settings Form --}}
        <form action="{{ route(\App\Models\SettingLocal::getLang().'.admin.cleanup.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
    
            <div class="form-group">
                <label for="inactive_period" class="block text-sm font-medium text-white">Inactive Period (days)</label>
                <input 
                    type="number" 
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                    id="inactive_period" 
                    name="inactive_period" 
                    value="{{ old('inactive_period', $cleanup->inactive_period) }}" 
                    required
                >
            </div>
    
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Update Settings
            </button>
        </form>
    </div>
</x-app-layout>
