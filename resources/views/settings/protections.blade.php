<x-app-layout>
    <x-slot name="header">
        <h2 class="semibold text-xl">
        {{__('Global Protections')}}
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
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Takeprofit Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Takeprofit Protection type</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Takeprofit value</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Stoploss Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Stoploss Protection Type</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Stoploss value</th>
                                        </tr>
                                    </thead>
                                    <tbody class="">
                                    @foreach ($protections as $protection)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $protection->slave_id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $protection->takeprofit_status }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $protection->takeprofit_type }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $protection->takeprofit_value }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $protection->stoploss_status }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $protection->stoploss_type }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $protection->stoploss_value }}</td>
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




                                        