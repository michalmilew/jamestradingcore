<x-app-layout>
    <x-slot name="header">
        <h2>
            {{__('Edit')}} {{__('Server')}}
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
							<tr class ="border-b py-2"><td>
                                <x-label for="name" :value="__('Name')" /></td>
                                <td> <x-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{$server->name}}" required autofocus />
                                </td>
                            </tr>

                            <tr class ="border-b py-2 right">
                                <td colspan="2">
                                    <x-button class="ml-3 mt-4">
                                    {{ __('Save') }} {{ __('Server') }}
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

