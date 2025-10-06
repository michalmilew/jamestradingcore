<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Notification Intervals') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-6 ">
            <div class="bg-dark-2 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <a href="{{ route(\App\Models\SettingLocal::getLang().'.admin.notification-intervals.create') }}" class="bg-red-500 hover:bg-red-700 text-gray font-bold py-2 px-4 h-full rounded">{{ __('Add New Interval') }}</a>
                    
                    <table class="mt-4 w-full">
                        <thead class="bg-gray-200 dark:bg-gray-700">
                            <tr>
                                <th class="py-2">{{ __('Command') }}</th>
                                <th class="py-2">{{ __('Interval') }}</th>
                                <th class="py-2">{{ __('Time') }}</th>
                                <th class="py-2">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($intervals as $interval)
                            <tr class="border-b dark:border-gray-600">
                                <td class="py-2">{{ $interval->command }}</td>
                                <td class="py-2">{{ __(ucfirst($interval->interval)) }}</td>
                                <td class="py-2">{{ $interval->time }}</td>
                                <td class="py-2">
                                    <a href="{{ route(\App\Models\SettingLocal::getLang().'.admin.notification-intervals.edit', $interval) }}" class="bg-blue-500 hover:bg-blue-700 text-gray font-bold py-1 px-3 rounded">{{ __('Edit') }}</a>
                                    <form action="{{ route(\App\Models\SettingLocal::getLang().'.admin.notification-intervals.destroy', $interval) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-gray font-bold py-1 px-3 rounded">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
