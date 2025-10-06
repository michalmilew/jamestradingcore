<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Create Notification Interval') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-6 ">
            <div class="bg-dark-2 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route(\App\Models\SettingLocal::getLang().'.admin.notification-intervals.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <x-label for="command" :value="__('Command')" />
                            <x-input id="command" class="block mt-1 w-full" type="text" name="command" required autofocus />
                        </div>

                        <div class="mb-4">
                            <x-label for="interval" :value="__('Interval')" />
                            <select id="interval" name="interval" class="block mt-1 w-full">
                                <option value="daily">{{ __('Daily') }}</option>
                                <option value="weekly">{{ __('Weekly') }}</option>
                                <option value="monthly">{{ __('Monthly') }}</option>
                                <option value="friday">{{ __('Friday') }}</option>
                                <option value="monday">{{ __('Monday') }}</option>
                                <option value="wednesday">{{ __('Wednesday') }}</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <x-label for="time" :value="__('Time')" />
                            <x-input id="time" class="block mt-1 w-full" type="time" name="time" value="00:00" required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Save Interval') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
