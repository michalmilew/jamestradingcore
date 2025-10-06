<x-app-layout>
    <x-slot name="header">
        <h2 class="semibold text-xl">
            {{__('Subscriptions')}}
        </h2>
    </x-slot>
    <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 ">
        <div class="overflow-hidden shadow-xl sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg leading-6 font-medium text-gray-900 mb-4">Subscriptions</h2>

                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 ">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                                Name
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                                Expiration Date
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                                Type
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                                Price
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                                Available Accounts
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                                Available Slaves
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                                Available Masters
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                                Equinix
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="">
                                        @foreach ($subscriptions as $subscription)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $subscription->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $subscription->expiration_date }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $subscription->type }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $subscription->price }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $subscription->price }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $subscription->price }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $subscription->price }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $subscription->price }}
                                            </td>
                                            
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




                                        