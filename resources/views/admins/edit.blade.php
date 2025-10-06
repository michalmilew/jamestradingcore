<x-app-layout>
    <x-slot name="header">
        <h2>
            {{__('Edit')}} {{__('User')}}
        </h2>
    </x-slot>
    <div class="py-5 px-8">
        <div class="max-w-7x1 mx-auto ">
            <div class=" ">
                <div class="overflow-x-auto justify-between rounded-lg px-2 py-2">
                    <div class="flex flex-col">
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    <form method="POST">
                    @csrf
                    <table>
							<tr class ="border-b py-2">
                                <td> <x-label for="name" :value="__('Name')" /></td>
                                <td> 
                                    <x-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{$admin->name}}" required autofocus />
                                </td>
                            </tr>
							<tr class ="border-b py-2">
                                <td><x-label for="email" :value="__('Email')" /></td>
                                <td><x-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{$admin->email}}" required /></td>
                            </tr>
                            <tr class ="border-b py-2">
                                <td><x-label for="password" :value="__('Password')" /></td>
                                <td><x-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    autocomplete="new-password" />
                                </td>
                            </tr>
                            <tr class ="border-b py-2">
                                <td><x-label for="password_confirmation" :value="__('Confirm Password')" /></td>
                                <td><x-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation"/></td>
                            </tr>

                            <tr class ="border-b py-2 right">
                                <td colspan="2">
                                    <x-button class="ml-3 mt-4">
                                    {{ __('Save') }} {{ __('Admin') }}
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

