<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-semibold leading-tight">
            {{ __('Manage Risk Settings') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-semibold mb-6">Risk Settings Management</h2>

        {{-- Display Success Message --}}
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
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

        {{-- Risk Settings Form --}}
        <form action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.risk-settings.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($riskSettings as $setting)
                    <div class="form-group p-4 border rounded-md shadow-sm">
                        <label for="risk_{{ $setting->name }}" class="block text-sm font-medium text-white mb-2">
                            {{ __(ucfirst($setting->name)) }} {{ __('Setting') }}
                        </label>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="risk_{{ $setting->name }}" name="risk_settings[{{ $setting->name }}][enabled]"
                                class="form-checkbox h-4 w-4 text-blue-600"
                                {{ old('risk_settings.' . $setting->name . '.enabled', $setting->enabled) ? 'checked' : '' }}>
                            <label for="risk_{{ $setting->name }}" class="ml-2 text-sm text-gray-300">
                                {{ __('Enabled') }}
                            </label>
                        </div>
                        <div class="mt-4 flex items-center space-x-4">
                            <label for="multiplier_{{ $setting->name }}" class="text-sm text-gray-300">
                                {{ __('Multiplier') }}
                            </label>
                            <input type="number" step="0.01" id="multiplier_{{ $setting->name }}" name="risk_settings[{{ $setting->name }}][multiplier]"
                                class="block w-24 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                value="{{ old('risk_settings.' . $setting->name . '.multiplier', $setting->multiplier) }}" required>
                        </div>
                        <div class="mt-4 flex items-center space-x-4">
                            <label for="min_deposit_{{ $setting->name }}" class="text-sm text-gray-300">
                                {{ __('Minimum Deposit (€)') }}
                            </label>
                            <input type="number" id="min_deposit_{{ $setting->name }}" name="risk_settings[{{ $setting->name }}][min_deposit]"
                                value="{{ $setting->min_deposit }}" required>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Update Risk Settings
            </button>
        </form>
    </div>
</x-app-layout>
