<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Select Notification Type and Language') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="mx-auto sm:px-6 ">
            <div class="bg-dark-2 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.notifications.redirect') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="mb-4">
                                <x-label for="notification_type" :value="__('Notification Type')" />
                                <select name="notification_type" id="notification_type" class="block mt-1 w-full" required>
                                    @foreach($notificationTypes as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <x-label for="language" :value="__('Language')" />
                                <select name="language" id="language" class="block mt-1 w-full" required>
                                    @foreach($languages as $lang)
                                        <option value="{{ $lang }}">{{ strtoupper($lang) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Edit Notification') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
