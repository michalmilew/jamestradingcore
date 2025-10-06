<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{-- Back Button --}}
            <a href="{{ route(\App\Models\SettingLocal::getLang() . '.admin.notifications.select') }}"
                class="inline-flex items-center px-4 py-2 bg-white-300 text-gray-800 font-semibold rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            {{ __('Edit :notificationType Translations for :language', ['notificationType' => $notificationType, 'language' => strtoupper($language)]) }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="mx-auto sm:px-6 ">
            <div class="bg-dark-2 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form
                        action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.notifications.update', [$notificationType, $language]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            @foreach ($translations as $key => $value)
                                <div class="mb-4">
                                    <x-label for="{{ $key }}" :value="__(ucwords(str_replace('_', ' ', $key)))" />
                                    <x-input id="{{ $key }}" class="block mt-1 w-full" type="text"
                                        name="translations[{{ $key }}]" value="{{ $value }}" required />
                                </div>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Update Translations') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
