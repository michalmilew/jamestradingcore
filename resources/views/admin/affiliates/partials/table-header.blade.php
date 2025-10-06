<thead class="bg-dark-1">
    <tr>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
            <a href="{{ route('affiliates.index', ['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1">
                <span>{{ __('User') }}</span>
                @if(request('sort') === 'name')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if(request('direction') === 'asc')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        @endif
                    </svg>
                @endif
            </a>
        </th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
            <a href="{{ route('affiliates.index', ['sort' => 'referral_price', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1">
                <span>{{ __('Referral Price') }}</span>
                @if(request('sort') === 'referral_price')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if(request('direction') === 'asc')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        @endif
                    </svg>
                @endif
            </a>
        </th>
        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">
            <a href="{{ route('affiliates.index', ['sort' => 'referral_count', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-center space-x-1">
                <span>{{ __('Referrals') }}</span>
                @if(request('sort') === 'referral_count')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if(request('direction') === 'asc')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        @endif
                    </svg>
                @endif
            </a>
        </th>
        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
            <a href="{{ route('affiliates.index', ['sort' => 'payable_balance', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-end space-x-1">
                <span>{{ __('Pending') }}</span>
                @if(request('sort') === 'payable_balance')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if(request('direction') === 'asc')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        @endif
                    </svg>
                @endif
            </a>
        </th>
        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
            <a href="{{ route('affiliates.index', ['sort' => 'paid_balance', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-end space-x-1">
                <span>{{ __('Paid') }}</span>
                @if(request('sort') === 'paid_balance')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if(request('direction') === 'asc')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        @endif
                    </svg>
                @endif
            </a>
        </th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
            {{ __('Actions') }}
        </th>
    </tr>
</thead> 