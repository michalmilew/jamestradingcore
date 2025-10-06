<x-app-layout>
    <x-slot name="header">
        <h2 class="semibold text-xl">
            {{__('reports')}}
        </h2>
    </x-slot>
    <div class="py-6">
    <div class="mx-auto sm:px-6 ">
        <div class= "overflow-hidden shadow-xl sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 ">
                            <div class="overflow-x-auto justify-between  rounded-lg px-2 py-2">
                                <div class="flex justify-between">
                                    <form class="flex-col items-center" method="get" id="sortForm" >
                                        <div class="flex justify-between mb-2">
                                            <input class="mr-2 rounded" type="text" name="search" value="{{ request('search') }}" placeholder="{{__('Search')}}">
                                            <input class="mr-2 rounded" type="date" name="fromdate" value="{{ request('fromdate') }}" placeholder="{{__('From date')}}">
                                            <input class="mr-2 rounded" type="date" name="todate" value="{{ request('todate') }}" placeholder="{{__('To date')}}">
                                            <button class="flex items-center gap-1 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                                    <circle cx="11" cy="11" r="8"></circle>
                                                    <path d="M21 21L16.65 16.65"></path>
                                                </svg>
                                                {{__('Search')}}
                                            </button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                            <div class="shadow overflow-hidden breport-b breport-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="">
                                    <tr>
                                        {{-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">{{__('User_ID')}}</th> --}}
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">{{__('Registration_Date')}}</th>
                                        {{-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">{{__('Tracking_Code')}}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">{{__('afp')}}</th> --}}
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">{{__('Qualification_Date')}}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">{{__('Country')}}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">{{__('Position_Count')}}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">{{__('Net_PL')}}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">{{__('Volume')}}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">{{__('First_Deposit')}}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">{{__('First_Deposit_Date')}}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">{{__('Withdrawals')}}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">{{__('generic1')}}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">{{__('Customer_Name')}}</th>
                                        {{-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">{{__('Commission')}}</th> --}}
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y ">
                                        @foreach ($reports as $report)
                                        <tr>
                                            {{-- <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $report["User_ID"] }}
                                            </td> --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                {{ $report["Registration_Date"] }}
                                            </td>
                                            {{-- <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $report["Tracking_Code"] }}
                                            </td> --}}
                                            {{-- <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ implode(", ", $report["afp"]) }}
                                            </td> --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ json_encode( $report["Qualification_Date"] )}}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $report["Country"] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $report["Position_Count"] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $report["Net_PL"] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $report["Volume"] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $report["First_Deposit"] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ json_encode( $report["First_Deposit_Date"] )}}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $report["Withdrawals"] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{  collect(explode("_",$report["generic1"]))->last() }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $report["Customer_Name"] }}
                                            </td>
                                            {{-- <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                                {{ $report["Commission"] }}
                                            </td> --}}
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="py-4">
                                    {{$reports->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
</x-app-layout>




