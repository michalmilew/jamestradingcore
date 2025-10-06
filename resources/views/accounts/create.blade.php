<x-app-layout>
    <x-slot name="header">
        <h2>
            {{ __('Add MetaTrader 4 Account') }}
        </h2>
    </x-slot>
    <div class="py-5 px-8">
        <div class="max-w-7x1 mx-auto ">
            <div class=" ">
                <div class="overflow-x-auto justify-between  rounded-lg px-2 py-1">
                    <div class="flex flex-col">
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        <form method="POST"
                            action="{{ route(\App\Models\SettingLocal::getLang() . '.password.email') }}">
                            @csrf
                            <div class="flex flex-col">
                                <div class ="py-1">
                                    <td>
                                        <x-label for="login" :value="__('account')" />
                                    </td>
                                    <td>
                                        <input type="number" name="login" id="login" class="w-[200px]">
                                    </td>
                                </div>
                                <div class ="py-1">
                                    <td>
                                        <x-label for="password" :value="__('password')" />
                                    </td>
                                    <td>
                                        <input type="password" name="password" id="password" class="w-[200px]">
                                    </td>
                                </div>
                                <div class ="py-1">
                                    <td>
                                        <x-label for="server" :value="__('server')" />
                                    </td>
                                    <td>
                                        <input type="text" name="server" id="server" class="w-[200px]">
                                    </td>
                                </div>
                                <div class ="py-1">
                                    <td>
                                        <x-label for="groupid" :value="__('setting')" />
                                    </td>
                                    <td>
                                        <select name="groupid" id="groupid"
                                            class="w-[200px] px-2 block mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <option value="LZZiiLZp">{{ __('High1') }}</option>
                                            <option value="EVZiiLZp">{{ __('Low1') }}</option>
                                            <option value="ppKiiLZp">{{ __('PRO+++') }}</option>
                                            <option value="LJKiiLZp">{{ __('PRO++') }}</option>
                                            <option value="OJKiiLZp">{{ __('PRO+') }}</option>
                                            <option value="wVZiiLZp">{{ __('PRO') }}</option>
                                            <option value="tXciiLZp">{{ __('High') }}</option>
                                            <option value="bXciiLZp">{{ __('Medium') }}</option>
                                            <option value="aXciiLZp">{{ __('Low') }}</option>
                                        </select>
                                    </td>
                                </div>

                                <div class ="py-1 right">
                                    <td colspan="2">
                                        <x-button class="mt-4">
                                            {{ __('Save') }} {{ __('Account') }}
                                        </x-button>
                                    </td>
                                </div>

                                </table>

                        </form>
                    </div>

                </div>

            </div>
        </div>

    </div>
</x-app-layout>
