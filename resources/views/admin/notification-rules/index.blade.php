<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Notification Rules') }}
        </h2>
    </x-slot>

    <div class="py-6 md:py-5">
        <div class="mx-auto sm:px-6 ">
            <div class="overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex h-10">
                        <a href="{{ route(\App\Models\SettingLocal::getLang() . '.admin.notification-rules.create') }}"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">{{ __('Add New Rule') }}</a>
                        <p class="py-3 px-6 text-sm">The risk type notification rule is influenced by the balance instead
                            of the PNL and margin type notification is influenced by the margin percent instead of PNL.
                        </p>
                    </div>

                    <div class="overflow-x-auto mt-4">
                        <table
                            class="min-w-full divide-y divide-gray-200 dark:divide-gray-600 hidden md:table bg-gray-600 text-gray-200">
                            <thead class="bg-gray-700 dark:bg-gray-600">
                                <tr>
                                    <th class="py-3 px-6 text-left text-sm font-medium">{{ __('Name') }}</th>
                                    <th class="py-3 px-6 text-left text-sm font-medium">{{ __('Type') }}</th>
                                    <th class="py-3 px-6 text-left text-sm font-medium">{{ __('Min Value') }}</th>
                                    <th class="py-3 px-6 text-left text-sm font-medium">{{ __('Max Value') }}</th>
                                    <th class="py-3 px-6 text-left text-sm font-medium">{{ __('Risk Level') }}</th>
                                    <th class="py-3 px-6 text-left text-sm font-medium">{{ __('Interval') }}</th>
                                    <th class="py-3 px-6 text-left text-sm font-medium">{{ __('Notification Class') }}
                                    </th>
                                    <th class="py-3 px-6 text-left text-sm font-medium">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rules as $rule)
                                    <tr class="border-b border-gray-700">
                                        <td class="py-3 px-6 text-sm">{{ $rule->name }}</td>
                                        <td class="py-3 px-6 text-sm">{{ __(ucfirst($rule->type)) }}</td>
                                        <td class="py-3 px-6 text-sm">{{ $rule->min_value }}</td>
                                        <td class="py-3 px-6 text-sm">{{ $rule->max_value }}</td>
                                        <td class="py-3 px-6 text-sm">{{ $rule->risk_level ?? __('N/A') }}</td>
                                        <td class="py-3 px-6 text-sm">{{ $rule->interval ?? __('N/A') }}</td>
                                        <td class="py-3 px-6 text-sm">{{ $rule->notification_class }}</td>
                                        <td class="py-3 px-6 text-sm">
                                            <a href="{{ route(\App\Models\SettingLocal::getLang() . '.admin.notification-rules.edit', $rule) }}"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">{{ __('Edit') }}</a>
                                            <form
                                                action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.notification-rules.destroy', $rule) }}"
                                                method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded">{{ __('Delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Mobile View -->
                        <div class="md:hidden">
                            @foreach ($rules as $rule)
                                <div class="bg-gray-700 border border-gray-600 rounded-lg shadow-sm mb-4 p-4">
                                    <div class="flex flex-col items-start">
                                        <div
                                            class="flex flex-col items-start justify-between text-sm font-medium text-gray-200">
                                            <span class="font-bold">{{ __('Name') }}:</span>
                                            <span>{{ $rule->name }}</span>
                                        </div>
                                        <div class="flex flex-col items-start justify-between text-sm text-gray-200">
                                            <span class="font-bold">{{ __('Type') }}:</span>
                                            <span>{{ ucfirst($rule->type) }}</span>
                                        </div>
                                        <div
                                            class="flex flex-col items-start items-start justify-between text-sm text-gray-200">
                                            <span class="font-bold">{{ __('Min Value') }}:</span>
                                            <span class="break-all">{{ $rule->min_value }}</span>
                                        </div>
                                        <div
                                            class="flex flex-col items-start items-start justify-between text-sm text-gray-200">
                                            <span class="font-bold">{{ __('Max Value') }}:</span>
                                            <span class="break-all">{{ $rule->max_value }}</span>
                                        </div>
                                        <div
                                            class="flex flex-col items-start items-start justify-between text-sm text-gray-200">
                                            <span class="font-bold">{{ __('Risk Level') }}:</span>
                                            <span class="break-all">{{ $rule->risk_level ?? __('N/A') }}</span>
                                        </div>
                                        <div
                                            class="flex flex-col items-start items-start justify-between text-sm text-gray-200">
                                            <span class="font-bold">{{ __('Interval') }}:</span>
                                            <span class="break-all">{{ $rule->interval ?? __('N/A') }}</span>
                                        </div>
                                        <div
                                            class="flex flex-col items-start items-start justify-between text-sm text-gray-200">
                                            <span class="font-bold">{{ __('Notification Class') }}:</span>
                                            <span class="break-all">{{ $rule->notification_class }}</span>
                                        </div>
                                    </div>
                                    <div class="flex justify-start mt-4 space-x-2">
                                        <a href="{{ route(\App\Models\SettingLocal::getLang() . '.admin.notification-rules.edit', $rule) }}"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">{{ __('Edit') }}</a>
                                        <form
                                            action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.notification-rules.destroy', $rule) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded">{{ __('Delete') }}</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
