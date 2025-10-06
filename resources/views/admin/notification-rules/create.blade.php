<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Add Notification Rule') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="mx-auto sm:px-6 ">
            <div class="bg-dark-2 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.notification-rules.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="mb-4">
                                <x-label for="name" :value="__('Rule Name')" />
                                <x-input id="name" class="block mt-1 w-full" type="text" name="name" required autofocus />
                            </div>

                            <div class="mb-4">
                                <x-label for="type" :value="__('Notification Type')" />
                                <select id="type" name="type" class="block mt-1 w-full">
                                    <option value="profit">{{ __('Profit') }}</option>
                                    <option value="invite">{{ __('Invite') }}</option>
                                    <option value="risk">{{ __('Risk') }}</option>
                                    <option value="margin">{{ __('Margin') }}</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <x-label for="min_value" :value="__('Min Value')" />
                                <x-input id="min_value" class="block mt-1 w-full" type="number" name="min_value" step="0.01" />
                            </div>

                            <div class="mb-4">
                                <x-label for="max_value" :value="__('Max Value')" />
                                <x-input id="max_value" class="block mt-1 w-full" type="number" name="max_value" step="0.01" />
                            </div>

                            <div class="mb-4">
                                <x-label for="risk_level" :value="__('Risk Level')" />
                                <select id="risk_level" name="risk_level" class="block mt-1 w-full">
                                    <option value="All">{{ __('All') }}</option>
                                    <option value="Low-High">{{ __('Low-High') }}</option>
                                    <option value="Low">{{ __('Low') }}</option>
                                    <option value="Medium">{{ __('Medium') }}</option>
                                    <option value="High">{{ __('High') }}</option>
                                    <option value="Pro">{{ __('Pro') }}</option>
                                    <option value="Pro+">{{ __('Pro+') }}</option>
                                    <option value="Pro++">{{ __('Pro++') }}</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <x-label for="interval" :value="__('Interval')" />
                                <select name="interval" id="interval" class="block mt-1 w-full">
                                    <option value="Daily">{{ __('Daily') }}</option>
                                    <option value="Weekly">{{ __('Weekly') }}</option>
                                    <option value="Monthly">{{ __('Monthly') }}</option>
                                    <option value="Monday">{{ __('Monday') }}</option>
                                    <option value="Tuesday">{{ __('Tuesday') }}</option>
                                    <option value="Wednesday">{{ __('Wednesday') }}</option>
                                    <option value="Thursday">{{ __('Thursday') }}</option>
                                    <option value="Friday">{{ __('Friday') }}</option>
                                    <option value="Saturday">{{ __('Saturday') }}</option>
                                    <option value="Sunday">{{ __('Sunday') }}</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <x-label for="notification_class" :value="__('Notification Class')" />
                                <x-input id="notification_class" class="block mt-1 w-full" type="text" name="notification_class" required />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Add Rule') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
