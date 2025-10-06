<x-app-layout>
    <x-slot name="header">
        <h2 class="semibold text-xl">
        {{$title}}
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
                <div class="py-2" style="padding-left:16px; background:linear-gradient(to right, rgb(17, 24, 39), rgb(75, 85, 99))">
                    <ul style='display:inline-block'>
                        <x-nav-link :href="route(\App\Models\SettingLocal::getLang().'.admin.positions.open')" :active="request()->routeIs(\App\Models\SettingLocal::getLang().'.admin.positions.open')">
                            {{ __('Open Positions') }}
                        </x-nav-link>
                    </ul> 
                    <ul  style='display:inline-block'>
                        <x-nav-link :href="route(\App\Models\SettingLocal::getLang().'.admin.positions.closed')" :active="request()->routeIs(\App\Models\SettingLocal::getLang().'.admin.positions.closed')">
                            {{ __('Closed Positions') }}
                        </x-nav-link>
                    </ul>
                </div>
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 ">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Account ID</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Master ID</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Ticket</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Ticket Master</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Open Time</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Side</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Symbol</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Open Price</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Stop Price</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Limit Price</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Stop Loss</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Take Profit</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Amount Lot</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Quantity CCY</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Swap CCY</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Account Currency</th>
                                        </tr>
                                    </thead>
                                    <tbody class="">
    @foreach($positions as $position)
    <tr>
        <td class="px-6 py-4 whitespace-nowrap">{{ $position->account_id }}</td>
        <td class="px-6 py-4 whitespace-nowrap">{{ $position->master_id }}</td>
        <td class="px-6 py-4 whitespace-nowrap">{{ $position->ticket }}</td>
        <td class="px-6 py-4 whitespace-nowrap">{{ $position->ticketMaster }}</td>
        <td class="px-6 py-4 whitespace-nowrap">{{ $position->openTime }}</td>
        <td class="px-6 py-4 whitespace-nowrap">{{ $position->side }}</td>
        <td class="px-6 py-4 whitespace-nowrap">{{ $position->symbol }}</td>
        <td class="px-6 py-4 whitespace-nowrap">{{ $position->openPrice }}</td>
        <td class="px-6 py-4 whitespace-nowrap">{{ $position->stopPrice }}</td>
        <td class="px-6 py-4 whitespace-nowrap">{{ $position->limitPrice }}</td>
        <td class="px-6 py-4 whitespace-nowrap">{{ $position->stopLoss }}</td>
        <td class="px-6 py-4 whitespace-nowrap">{{ $position->takeProfit }}</td>
        <td class="px-6 py-4 whitespace-nowrap">{{ $position->amountLot }}</td>
        <td class="px-6 py-4 whitespace-nowrap">{{ $position->quantityCcy }}</td>
        <td class="px-6 py-4 whitespace-nowrap">{{ $position->swapCcy }}</td>
        <td class="px-6 py-4 whitespace-nowrap">{{ $position->ccy }}</td>
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




                                        