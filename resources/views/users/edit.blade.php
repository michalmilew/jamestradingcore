<x-app-layout>
    <x-slot name="header">
        <h2>
            {{__('Edit')}} {{__('User')}}
        </h2>
    </x-slot>
    <div class="py-5 px-8">
        <div class="max-w-7x1 mx-auto ">
            <div class=" ">
                <div class="overflow-x-auto justify-between  rounded-lg px-2 py-2">
                    <div class="flex flex-col">
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        <form method="POST">
                            @csrf
                            <table>
                                <tr class ="py-2">
                                    <td> <x-label for="name" :value="__('Name')" /></td>
                                    <td>
                                        <x-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{$user->name}}" required autofocus />
                                    </td>
                                </tr>
                                <tr class ="py-2">
                                    <td><x-label for="email" :value="__('Email')" /></td>
                                    <td><x-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{$user->email}}" required /></td>
                                </tr>
                                <tr class ="py-2"><td>
                                    <x-label for="lang" :value="__('Language')" /></td><td>
                                    <select name="lang" id="lang">
                                    <option value="" >{{__('Select')}}</option>
                                        @foreach(\App\Models\SettingLocal::getLangs() as $key => $value)
                                            <option value="{{$key}}" {{ $user->lang == $key ? 'selected' : '' }}>{{$value}}</option>
                                        @endforeach
                                    </select>
                                </td></tr>
                                <tr class="py-2">
                                    <td><x-label for="is_vip" :value="__('PRO')" /></td>
                                    <td>
                                        <select name="is_vip" id="is_vip">
                                            <option value="0" >{{__('No')}}</option>
                                            <option value="1" {{($user->is_vip == 1) ? 'selected':''}}>{{__('PRO')}}</option>
                                            <option value="2" {{($user->is_vip == 2) ? 'selected':''}}>{{__('PRO+')}}</option>
                                            <option value="3" {{($user->is_vip == 3) ? 'selected':''}}>{{__('PRO++')}}</option>
                                            <option value="4" {{($user->is_vip == 4) ? 'selected':''}}>{{__('PRO+++')}}</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr class ="py-2 hidden">
                                    <td><x-label for="password" :value="__('Password')" /></td>
                                    <td><x-input id="password" class="block mt-1 w-full"
                                        type="password"
                                        name="password"
                                        autocomplete="new-password" />
                                    </td>
                                </tr>
                                <tr class ="py-2 hidden">
                                    <td><x-label for="password_confirmation" :value="__('Confirm Password')" /></td>
                                    <td><x-input id="password_confirmation" class="block mt-1 w-full"
                                    type="password"
                                    name="password_confirmation"/></td>
                                </tr>

                                <tr class="py-2">
                                    <td>
                                        <x-label for="notes" :value="__('Notes')" />
                                    </td>
                                    <td>
                                        <x-input id="notes" class="block mt-1 w-full" type="text" name="notes" value="{{$user->notes}}" />
                                    </td>
                                </tr>

                                <tr class="py-2">
                                    <td>
                                        <x-label for="ig_user" :value="__('IG User')" />
                                    </td>
                                    <td>
                                        <x-input id="ig_user" class="block mt-1 w-full" type="text" name="ig_user" value="{{$user->ig_user}}" required />
                                    </td>
                                </tr>

                                <tr class="py-2">
                                    <td>
                                        <x-label for="id_broker" :value="__('ID Broker')" />
                                    </td>
                                    <td>
                                        <x-input id="id_broker" class="block mt-1 w-full" type="text" name="id_broker" value="{{$user->id_broker}}" required />
                                    </td>
                                </tr>

                                <tr class="py-2">
                                    <td>
                                        <x-label for="ftd" :value="__('FTD')" />
                                    </td>
                                    <td>
                                        <x-input id="ftd" class="block mt-1 w-full" type="text" name="ftd" value="{{$user->ftd}}" required />
                                    </td>
                                </tr>
                                <tr class="py-2">
                                    <td>
                                        <x-label for="paid" :value="__('Paid')" />
                                    </td>
                                    <td>
                                        <select id="paid" class="block mt-1 w-full" name="paid" value="{{$user->paid}}">
                                            <option value="" >{{__('Select')}}</option>
                                            <option value="Yes" {{ $user->paid == 'Yes' ? 'selected' : '' }}>{{__('Yes')}}</option>
                                            <option value="No" {{ $user->paid == 'No' ? 'selected' : '' }}>{{__('No')}}</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr class="py-2">
                                    <td>
                                        <x-label for="broker" :value="__('Broker')" />
                                    </td>
                                    <td>
                                        <select id="broker" class="block mt-1 w-full" name="broker" value="{{$user->broker}}" required>
                                            <option value="">{{__('Select')}}</option>
                                            <option value="IronFX"  {{ $user->broker == 'IronFX' ? 'selected' : '' }}>IronFX</option>
                                            <option value="T4Trade"  {{ $user->broker == 'T4Trade' ? 'selected' : '' }}>T4Trade</option>
                                            <option value="Other"  {{ $user->broker == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr class ="py-2 right">
                                    <td colspan="2">
                                        <x-button class="ml-3 mt-4">
                                        {{ __('Save') }} {{ __('User') }}
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

