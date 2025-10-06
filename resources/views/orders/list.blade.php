<x-app-layout>
    <x-slot name="header">
        <h2 class="semibold text-xl">
            {{__('Orders')}}
        </h2>
    </x-slot>
    <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 ">
        <div class= overflow-hidden shadow-xl sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg leading-6 font-medium text-gray-900 mb-4">Orders</h2>

                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 ">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Date/Time</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Master ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Master Trade ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Slave Account ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Side</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Action</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Symbol</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Quantity Requested</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Stop Loss</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Take Profit</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Quantity Executed</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Quantity Executed (EUR)</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Quantity Executed (USD)</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Price Executed</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Status ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Status Text</th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y ">
                                        @foreach ($orders as $order)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                {{ $order->timestamp }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $order->master_id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $order->ticketMaster }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $order->account_id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $order->side }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $order->action }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $order->symbol }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $order->quantityOrder }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $order->stopLoss }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $order->takeProfit }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $order->quantityExecuted }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $order->quantityExecutedEUR }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $order->quantityExecutedUSD }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $order->priceExecuted }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $order->status_id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $order->statusName }}
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




                                        