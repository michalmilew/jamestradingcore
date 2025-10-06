<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-white">
            {{ __('Affiliates Management') }}
        </h2>
    </x-slot>

    {{-- Alert Container --}}
    <div id="alert-container" class="fixed top-4 right-4 z-50"></div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-dark-1 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    {{-- Search Form --}}
                    @include('admin.affiliates.partials.search-form')

                    {{-- Default Referral Price Section --}}
                    @include('admin.affiliates.partials.default-price')

                    {{-- Affiliates Table (Desktop Only) --}}
                    <div class="hidden md:block overflow-x-auto bg-dark-2 rounded-lg">
                        <div class="min-w-full inline-block align-middle">
                            <div class="overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-700">
                                    @include('admin.affiliates.partials.table-header')
                                    <tbody class="divide-y divide-gray-700">
                                        @forelse($affiliates as $affiliate)
                                            <tr class="hover:bg-dark-1 transition-colors duration-150">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-white">{{ $affiliate->name }}</div>
                                                    <div class="text-sm text-gray-400">{{ $affiliate->email }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($affiliate->referral_price)
                                                        €{{ number_format($affiliate->referral_price, 2) }}
                                                    @else
                                                        {{ __('Default') }}
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    {{ $affiliate->referral_count }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <span class="text-yellow-500">€{{ number_format($affiliate->payable_balance ?? 0, 2) }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <span class="text-green-500">€{{ number_format($affiliate->paid_balance ?? 0, 2) }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @include('admin.affiliates.partials.row-actions')
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-4 text-center text-gray-400">
                                                    {{ __('No affiliates found') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Mobile View Cards (Mobile Only) --}}
                    <div class="md:hidden space-y-4">
                        @forelse($affiliates as $affiliate)
                            <div class="bg-dark-2 rounded-lg p-4 space-y-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-medium text-white">{{ $affiliate->name }}</h3>
                                        <p class="text-sm text-gray-400">{{ $affiliate->email }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-400">{{ __('Referral Price') }}</p>
                                        <p class="text-white">
                                            @if($affiliate->referral_price)
                                                €{{ number_format($affiliate->referral_price, 2) }}
                                            @else
                                                {{ __('Default') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-400">{{ __('Referrals') }}</p>
                                        <p class="text-white">{{ $affiliate->referral_count }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-400">{{ __('Pending') }}</p>
                                        <p class="text-yellow-500">€{{ number_format($affiliate->payable_balance ?? 0, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-400">{{ __('Paid') }}</p>
                                        <p class="text-green-500">€{{ number_format($affiliate->paid_balance ?? 0, 2) }}</p>
                                    </div>
                                </div>

                                <div class="pt-2">
                                    @include('admin.affiliates.partials.row-actions')
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-400 py-4">
                                {{ __('No affiliates found') }}
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $affiliates->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('admin.affiliates.partials.payment-modal')
    @include('admin.affiliates.partials.referral-amount-modal')
    @include('admin.affiliates.partials.referral-history-modal')

    {{-- Scripts --}}
    @push('scripts')
        <script src="{{ asset('js/affiliates.js') }}"></script>
    @endpush
</x-app-layout>