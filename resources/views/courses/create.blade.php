<x-app-layout>
    <x-slot name="header">
        <h2>
            {{__('New')}} {{__('Course')}}
        </h2>
    </x-slot>
    <div class="py-5 px-8">
        <div class="max-w-7x1 mx-auto ">
            <div class=" ">
                <div class="overflow-x-auto justify-between rounded-lg px-2 py-2">
                    <div class="flex flex-col">
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    <form method="POST" enctype="multipart/form-data">
                    @csrf
                    <table>
                        <tr class ="py-2">
                            <td>
                                <x-label for="name" :value="__('Tilte')" /></td>
                                <td> <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                </td>
                            </tr>
                            <td>
                                <x-label for="url" :value="__('URL')" /></td>
                                <td> <x-input id="url" class="block mt-1 w-full" type="text" name="url" :value="old('url')" required autofocus />
                                </td>
                            </tr>

                            <tr class ="py-2 my-2 hidden">
                                <td class =" py-2 my-2">
                                    <x-label for="name" :value="__('File')" />
                                </td>
                                <td>
                                    <x-input id="pdf_file" class="block mt-1 w-full" type="file" name="pdf_file"  autofocus />
                                </td>
                            </tr>


                            <tr class ="py-2 my-2 ">
                                <td class =" py-2 my-2">
                                    <x-label for="lang" :value="__('Languge')" />
                                </td>
                                <td>
                                    <select id="lang" class="block mt-1 w-full" name="lang" value="{{old('lang')}}" >
                                        <option value="">{{__('Select')}}</option>
                                        @foreach(\App\Models\SettingLocal::getLangs() as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr class ="py-2 right">
                                <td colspan="2">
                                    <x-button class="ml-3 mt-4">
                                    {{ __('Save') }} {{ __('Course') }}
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

