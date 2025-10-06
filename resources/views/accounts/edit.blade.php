<x-app-layout>
    <x-slot name="header">
        <h2>
            {{ __('Edit') }} {{ __('MetaTrader 4 Account') }}
        </h2>
    </x-slot>
    <div class="py-5 px-8">
        <div class="max-w-7x1 mx-auto ">
            <div class=" ">
                <div class="overflow-x-auto justify-between  rounded-lg px-2 py-2">
                    <div class="flex flex-col">
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        <form method="POST"
                            action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.accounts.update', $account->account_id) }}">
                            @csrf
                            <input type="hidden" name="account_id" value="{{ $account->account_id }}">
                            <table>
                                <tr class="mb-1 flex md:table-row flex-col">
                                    <td>
                                        <x-label for="login" :value="__('Account')" />
                                    </td>
                                    <td>
                                        <input type="text" name="login" id="login"
                                            value="{{ $account->login }}">
                                    </td>
                                </tr>
                                <tr class="mb-1 flex md:table-row flex-col">
                                    <td>
                                        <x-label for="password" :value="__('Password')" />
                                    </td>
                                    <td>
                                        <input type="password" name="password" id="password"
                                            value="{{ $account->password }}">
                                    </td>
                                </tr>
                                <tr class="mb-1 flex md:table-row flex-col">
                                    <td>
                                        <x-label for="server" :value="__('Server')" />
                                    </td>
                                    <td>
                                        <input type="text" name="server" id="server"
                                            value="{{ $account->server }}">
                                </tr>
                                <tr class ="mb-1 flex md:table-row flex-col">
                                    <td>
                                        <x-label for="group_id" :value="__('Setting')" />
                                    </td>
                                    <td>
                                        <select name="groupid" id="groupid" class="w-full">
                                            <option value="LZZiiLZp"
                                                {{ $account->groupid == 'LZZiiLZp' ? 'selected' : '' }}>
                                                {{ __('High1') }}</option>
                                            <option value="EVZiiLZp"
                                                {{ $account->groupid == 'EVZiiLZp' ? 'selected' : '' }}>
                                                {{ __('Low1') }}</option>
                                            <option value="ppKiiLZp"
                                                {{ $account->groupid == 'ppKiiLZp' ? 'selected' : '' }}>
                                                {{ __('PRO+++') }}</option>
                                            <option value="LJKiiLZp"
                                                {{ $account->groupid == 'LJKiiLZp' ? 'selected' : '' }}>
                                                {{ __('PRO++') }}</option>
                                            <option value="OJKiiLZp"
                                                {{ $account->groupid == 'OJKiiLZp' ? 'selected' : '' }}>
                                                {{ __('PRO+') }}</option>
                                            <option value="wVZiiLZp"
                                                {{ $account->groupid == 'wVZiiLZp' ? 'selected' : '' }}>
                                                {{ __('PRO') }}</option>
                                            <option value="tXciiLZp"
                                                {{ $account->groupid == 'tXciiLZp' ? 'selected' : '' }}>
                                                {{ __('High') }}</option>
                                            <option value="bXciiLZp"
                                                {{ $account->groupid == 'bXciiLZp' ? 'selected' : '' }}>
                                                {{ __('Medium') }}</option>
                                            <option value="aXciiLZp"
                                                {{ $account->groupid == 'aXciiLZp' ? 'selected' : '' }}>
                                                {{ __('Low') }}</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr class ="py-2 right">
                                    <td colspan="2">
                                        <x-button class="mt-4">
                                            {{ __('Save') }} {{ __('Account') }}
                                        </x-button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>

                </div>

            </div>
        </div>

    </div>
</x-app-layout>
