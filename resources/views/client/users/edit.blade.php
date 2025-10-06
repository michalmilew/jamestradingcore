<x-app-layout>
    <x-slot name="header">
        <h2>
            {{__('Edit')}} {{__('User')}}
        </h2>
    </x-slot>
    <div class="p-5">
        <div class="max-w-7x1 mx-auto ">
            <div class=" ">
                <div class="overflow-x-auto justify-between rounded-lg px-2 py-2">
                    <div class="flex flex-col">
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    <form method="POST">
                    @csrf
                    <table>
							<tr class ="py-2">
                                <td> <x-label for="name" :value="__('Name')" /></td>
                                <td> {{$user->name}}</td>
                            </tr>
							<tr class ="py-2">
                                <td><x-label for="email" :value="__('Email')" /></td>
                                <td>{{$user->email}}</td>
                            </tr>
                            <tr class ="py-2"><td>
                                <x-label for="lang" :value="__('Language')" /></td><td>
                                <select name="lang" id="lang" class="w-full p-2">
                                <option value="pt" {{ $user->lang == 'pt' ? 'selected' : '' }}>{{__('Português')}}</option>
                                <option value="en" {{ $user->lang == 'en' ? 'selected' : '' }}>{{__('English')}}</option>
                                </select>
                            </td></tr>
                            <tr class ="py-2">
                                <td><x-label for="current_password" :value="__('Current Password')" /></td>
                                <td><x-input id="current_password" class="block mt-1 w-full"
                                    type="password"
                                    name="current_password"
                                    autocomplete="current-password" />
                                </td>
                            </tr>
                            <tr class ="py-2">
                                <td><x-label for="password" :value="__('Password')" /></td>
                                <td><x-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    autocomplete="new-password" />
                                </td>
                            </tr>
                            <tr class ="py-2">
                                <td><x-label for="password_confirmation" :value="__('Confirm Password')" /></td>
                                <td><x-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation"/></td>
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

