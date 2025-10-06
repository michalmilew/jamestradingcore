<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Edit Notification Interval') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-6 ">
            <div class="bg-dark-2 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route(\App\Models\SettingLocal::getLang().'.admin.notification-intervals.update', $notificationInterval) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <x-label for="command" :value="__('Command')" />
                            <x-input id="command" class="block mt-1 w-full" type="text" name="command" value="{{ $notificationInterval->command }}" required autofocus />
                        </div>

                        <div class="mb-4">
                            <x-label for="interval" :value="__('Interval')" />
                            <select id="interval" name="interval" class="block mt-1 w-full">
                                <option value="daily" {{ $notificationInterval->interval == 'daily' ? 'selected' : '' }}>{{ __('Daily') }}</option>
                                <option value="weekly" {{ $notificationInterval->interval == 'weekly' ? 'selected' : '' }}>{{ __('Weekly') }}</option>
                                <option value="monthly" {{ $notificationInterval->interval == 'monthly' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                                <option value="friday" {{ $notificationInterval->interval == 'friday' ? 'selected' : '' }}>{{ __('Friday') }}</option>
                                <option value="monday" {{ $notificationInterval->interval == 'monday' ? 'selected' : '' }}>{{ __('Monday') }}</option>
                                <option value="wednesday" {{ $notificationInterval->interval == 'wednesday' ? 'selected' : '' }}>{{ __('Wednesday') }}</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <x-label for="time" :value="__('Time')" />
                            <x-input id="time" class="block mt-1 w-full" type="time" name="time" value="{{ $notificationInterval->time }}" required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Update Interval') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
