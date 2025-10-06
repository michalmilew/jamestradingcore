<x-app-layout>
    <x-slot name="header">
        <h2 class="semibold text-xl">
        {{__('Slave / Group Settings')}}
        </h2>
        @if (session('error'))
            <x-error>
            {{ session('error') }}
            </x-error>                
        @endif  
    </x-slot>
    <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 ">
        <div class="overflow-hidden shadow-xl sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 ">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="">
                                        <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Slave account_id</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Group id</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Master account_id</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Risk Factor value</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Risk Factor type</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Max order size in lot/volume</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Copier status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="">
                                    @foreach ($settings as $setting)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $setting->slave_id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $setting->group_id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $setting->master_id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $setting->risk_factor_value }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $setting->risk_factor_type_name }}</td>
                                            <td class="px-6 py-4 whitespacwee-nowrap">{{ $setting->max_order_size }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $setting->copier_status_name }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
    </div>
</div>
</x-app-layout>




                                        