<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Edit Notification Rule') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="mx-auto sm:px-6 ">
            <div class="bg-dark-2 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.notification-rules.update', $notificationRule) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="mb-4">
                                <x-label for="name" :value="__('Rule Name')" />
                                <x-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ $notificationRule->name }}" required autofocus />
                            </div>

                            <div class="mb-4">
                                <x-label for="type" :value="__('Notification Type')" />
                                <select id="type" name="type" class="block mt-1 w-full">
                                    <option value="profit" {{ $notificationRule->type == 'profit' ? 'selected' : '' }}>{{ __('Profit') }}</option>
                                    <option value="invite" {{ $notificationRule->type == 'invite' ? 'selected' : '' }}>{{ __('Invite') }}</option>
                                    <option value="risk" {{ $notificationRule->type == 'risk' ? 'selected' : '' }}>{{ __('Risk') }}</option>
                                    <option value="margin" {{ $notificationRule->type == 'margin' ? 'selected' : '' }}>{{ __('Margin') }}</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <x-label for="min_value" :value="__('Min Value')" />
                                <x-input id="min_value" class="block mt-1 w-full" type="number" name="min_value" step="0.01" value="{{ $notificationRule->min_value }}" />
                            </div>

                            <div class="mb-4">
                                <x-label for="max_value" :value="__('Max Value')" />
                                <x-input id="max_value" class="block mt-1 w-full" type="number" name="max_value" step="0.01" value="{{ $notificationRule->max_value }}" />
                            </div>

                            <div class="mb-4">
                                <x-label for="risk_level" :value="__('Risk Level')" />
                                <select id="risk_level" name="risk_level" class="block mt-1 w-full">
                                    <option value="All" {{ $notificationRule->risk_level == 'All' ? 'selected' : '' }}>{{ __('All') }}</option>
                                    <option value="Low-High" {{ $notificationRule->risk_level == 'Low-High' ? 'selected' : '' }}>{{ __('Low-High') }}</option>
                                    <option value="Low" {{ $notificationRule->risk_level == 'Low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                                    <option value="Medium" {{ $notificationRule->risk_level == 'Medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                                    <option value="High" {{ $notificationRule->risk_level == 'High' ? 'selected' : '' }}>{{ __('High') }}</option>
                                    <option value="Pro" {{ $notificationRule->risk_level == 'Pro' ? 'selected' : '' }}>{{ __('Pro') }}</option>
                                    <option value="Pro+" {{ $notificationRule->risk_level == 'Pro+' ? 'selected' : '' }}>{{ __('Pro+') }}</option>
                                    <option value="Pro++" {{ $notificationRule->risk_level == 'Pro++' ? 'selected' : '' }}>{{ __('Pro++') }}</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <x-label for="interval" :value="__('Interval')" />
                                <select name="interval" id="interval" class="block mt-1 w-full">
                                    <option value="Daily" {{ $notificationRule->interval == 'Daily' ? 'selected' : '' }}>{{ __('Daily') }}</option>
                                    <option value="Weekly" {{ $notificationRule->interval == 'Weekly' ? 'selected' : '' }}>{{ __('Weekly') }}</option>
                                    <option value="Monthly" {{ $notificationRule->interval == 'Monthly' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                                    <option value="Monday" {{ $notificationRule->interval == 'Monday' ? 'selected' : '' }}>{{ __('Monday') }}</option>
                                    <option value="Tuesday" {{ $notificationRule->interval == 'Tuesday' ? 'selected' : '' }}>{{ __('Tuesday') }}</option>
                                    <option value="Wednesday" {{ $notificationRule->interval == 'Wednesday' ? 'selected' : '' }}>{{ __('Wednesday') }}</option>
                                    <option value="Thursday" {{ $notificationRule->interval == 'Thursday' ? 'selected' : '' }}>{{ __('Thursday') }}</option>
                                    <option value="Friday" {{ $notificationRule->interval == 'Friday' ? 'selected' : '' }}>{{ __('Friday') }}</option>
                                    <option value="Saturday" {{ $notificationRule->interval == 'Saturday' ? 'selected' : '' }}>{{ __('Saturday') }}</option>
                                    <option value="Sunday" {{ $notificationRule->interval == 'Sunday' ? 'selected' : '' }}>{{ __('Sunday') }}</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <x-label for="notification_class" :value="__('Notification Class')" />
                                <x-input id="notification_class" class="block mt-1 w-full" type="text" name="notification_class" value="{{ $notificationRule->notification_class }}" required />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Update Rule') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
