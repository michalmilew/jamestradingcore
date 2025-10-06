<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-6 ">
            <div class="bg-dark-2 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 ">
                    {{__('The administrative notification will be sent to: ')}} 
                    {{\App\Models\SettingLocal::getAdminEmail()}}.

                    <!-- Modal toggle -->
                    <button data-modal-target="defaultModal" data-modal-toggle="defaultModal" class="bg-red-500 hover:bg-red-700 text-gray font-bold py-2 px-4  h-full rounded" type="button">
                        {{__('Change')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div>
        
                    <!-- Main modal -->
                    <div id="defaultModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-2xl max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                <!-- Modal header -->
                                <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    {{__('Changing Admin email')}}
                                    </h3>
                                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="defaultModal">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                                <!-- Modal body -->
                                <div class="p-6 space-y-6">
                                    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                        <form action="{{ route(\App\Models\SettingLocal::getLang().'.admin.settingsetparams') }}" method="POST">
                                            @csrf
                                            <table>
                                                <tr class="border-b py-2">
                                                    <td>
                                                        <x-label for="email" :value="__('Admin').' '.__('Email')" />
                                                    </td>
                                                    <td>
                                                        <input type="email" name="email" id="email" value="{{\App\Models\SettingLocal::getAdminEmail()}}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><button type="submit" class="bg-red-500 hover:bg-red-700 text-gray font-bold py-2 px-4  h-full rounded">{{__('Save')}}</button></td></tr>
                                                
                                            
                                        </form>  
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
    </div>
</x-app-layout>
