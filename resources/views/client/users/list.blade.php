<x-app-layout>
    <x-slot name="header">
        <h2 class="semibold text-xl">
            {{ __('Users') }}
        </h2>
    </x-slot>

    <div class="p-5">
        <div class="max-w-7x1 mx-auto px-8">
            <div class=" ">
                <div class="overflow-x-auto rounded-lg px-2 mt-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ($users as $user)
                                    <div class="rounded-md shadow-md p-4">
                                        <div class="font-bold mb-2">{{ __('Name') }}:</div>
                                        <div class="mb-4">{{ $user->name }}</div>
                                        <div class="font-bold mb-2">{{ __('Email') }}:</div>
                                        <div class="mb-4">{{ $user->email }}</div>
                                        <div class="font-bold mb-2">{{ __('PRO') }}:</div>
                                        <div class="mb-4">
                                            {{ $user->is_vip == 1 ? __('Yes') . ' ' . __('PRO') : ($user->is_vip == 2 ? __('Yes') . ' ' . __('PRO+') : ($user->is_vip == 3 ? __('Yes') . ' ' . __('PRO++') : ($user->is_vip == 4 ? __('Yes') . ' ' . __('PRO+++') : __('No')))) }}
                                        </div>
                                        <div class="font-bold mb-2">{{ __('Language') }}:</div>
                                        <div class="mb-4">{{ $user->lang }}</div>
                                        <div class="flex justify-end">
                                            @if (Route::has(route(\App\Models\SettingLocal::getLang() . '.client.users.edit', $user->id)))
                                                <a href="{{ route(\App\Models\SettingLocal::getLang() . '.client.users.edit', $user->id) }}"
                                                    class="bg-green-500 hover:bg-green-700 text-gray font-bold py-2 px-4 rounded">{{ __('Edit') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
