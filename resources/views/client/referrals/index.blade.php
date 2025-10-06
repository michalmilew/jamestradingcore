<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl md:text-2xl font-bold text-white">{{ __('Referral Program') }}</h1>
    </x-slot>
    <div class="p-5">
        <div class="max-w-4xl mx-auto">
            {{-- Hero Section --}}
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-8 mb-8 shadow-lg relative overflow-hidden">
                <div class="absolute inset-0 bg-grid-white/[0.05]"></div>
                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row items-center justify-between">
                        <div class="text-center md:text-left mb-6 md:mb-0">
                            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">
                                {{ __('Earn Big with Our Referral Program!') }}
                            </h1>
                            <p class="text-lg text-blue-100 mb-4">
                                {{ __('Invite friends and earn') }}
                                <span class="text-2xl font-bold text-yellow-300">€{{ number_format($userReferralPrice, 2) }}</span>
                                {{ __('for each friend!') }}
                            </p>
                            <p class="text-blue-100">
                                {{ __('No limits - the more friends you invite, the more you earn!') }}
                            </p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 text-center">
                            <div class="text-4xl font-bold text-yellow-300 mb-2">€{{ number_format($defaultReferralPrice, 2) }}</div>
                            <div class="text-blue-100">{{ __('Default Price Per Referral') }}</div>
                        </div>
                    </div>
                </div>
                {{-- Decorative elements --}}
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl"></div>
            </div>

            <!-- Balance Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="bg-dark-1 rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-2">{{ __('Amount pending payment') }}</h3>
                    <p class="text-2xl font-bold text-yellow-500">€{{ number_format($pendingAmount, 2) }}</p>
                </div>
                <div class="bg-dark-1 rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-2">{{ __('Total amount already paid') }}</h3>
                    <p class="text-2xl font-bold text-purple-500">€{{ number_format($totalPaid, 2) }}</p>
                </div>
            </div>

            <!-- Referral Form -->
            <div class="bg-dark-1 rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">{{ __('Submit Referral') }}</h2>
                <p class="mb-4">
                    {{ __('To receive your referral bonus, your friend must send me a message to register with the broker and join the platform.') }}
                </p>
                <p class="mb-4">
                    {{ __('Once they join, enter their email below to automatically receive the :amount€ bonus.', ['amount' => $userReferralPrice]) }}
                </p>

                <form id="referralForm" class="space-y-4">
                    @csrf
                    <div>
                        <label for="email"
                            class="block text-sm font-medium text-gray-300">{{ __('Friend\'s Email') }}</label>
                        <input type="email" name="email" id="email" required
                            class="mt-1 block w-full rounded-md border-gray-600 bg-dark-2 text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Submit Referral') }}
                    </button>
                </form>
            </div>

            <!-- Payment Request -->
            <div class="bg-dark-1 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">{{ __('Request Payment') }}</h2>
                <p class="mb-4">
                    {{ __('To request a payout of your Payable Balance, send a message through Telegram.') }}</p>
                <a href="https://t.me/JamesPereira99" target="_blank" class="btn btn-primary">
                    {{ __('Request Payment') }}
                </a>
            </div>

            <!-- Add this after the Payment Request section -->
            <div class="bg-dark-1 rounded-lg p-6 mt-8">
                <h2 class="text-xl font-semibold mb-4">{{ __('Your Referrals') }}</h2>
                <div class="referrals-list space-y-2">
                    @foreach($referrals as $referral)
                        <div class="bg-dark-2 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-gray-400">{{ $referral->referred_email }}</p>
                                    <p class="text-{{ $referral->status === 'pending' ? 'yellow' : 'green' }}-500">
                                        €{{ number_format($referral->amount, 2) }}
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full {{ $referral->status === 'pending' ? 'bg-yellow-500' : 'bg-green-500' }} text-dark-1">
                                    {{ $referral->status === 'pending' ? __('Pending') : __('Paid') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Loading Overlay -->
        <div class="loading-overlay" id="loading-overlay">
            <img src="{{ asset('images/loading.gif') }}" alt="Loading..." class="loading-gif">
        </div>
    
        <!-- Add this modal HTML structure just before -->
        <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-dark-1 rounded-lg p-6 max-w-sm mx-4">
                <h3 id="modalTitle" class="text-lg font-bold mb-4"></h3>
                <p id="modalMessage" class="mb-4"></p>
                <button onclick="closeModal()" class="btn btn-primary">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.getElementById('referralForm');
                    const loadingOverlay = document.getElementById('loading-overlay');
                    const modal = document.getElementById('modal');
                    const pendingAmountElement = document.querySelector('[data-pending-amount]');
                    
                    window.showLoading = function() {
                        loadingOverlay.style.display = 'flex';
                    };
    
                    window.hideLoading = function() {
                        loadingOverlay.style.display = 'none';
                    };
    
                    window.showModal = function(title, message) {
                        document.getElementById('modalTitle').textContent = title;
                        document.getElementById('modalMessage').textContent = message;
                        modal.style.display = 'flex';
                    };
    
                    window.closeModal = function() {
                        modal.style.display = 'none';
                    };
    
                    // Function to update the pending amount display
                    function updatePendingAmount(newAmount) {
                        const pendingAmountDisplay = document.querySelector('.text-2xl.font-bold.text-yellow-500');
                        if (pendingAmountDisplay) {
                            pendingAmountDisplay.textContent = '€' + parseFloat(newAmount).toFixed(2);
                        }
                    }
    
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        showLoading();
    
                        try {
                            const response = await fetch('{{ route(app()->getLocale() . ".client.referrals.store") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    email: form.email.value
                                })
                            });
    
                            const data = await response.json();
                            hideLoading();
    
                            if (data.success) {
                                showModal('{{ __("Success") }}', data.message);
                                form.reset();
    
                                // Update the pending amount immediately
                                const currentPendingAmount = parseFloat(document.querySelector('.text-2xl.font-bold.text-yellow-500').textContent.replace('€', ''));
                                const newReferralAmount = parseFloat(data.referral.amount);
                                const newPendingAmount = currentPendingAmount + newReferralAmount;
                                updatePendingAmount(newPendingAmount);
    
                                // Add the new referral to the list if it exists
                                if (data.referral) {
                                    const referralsList = document.querySelector('.referrals-list');
                                    if (referralsList) {
                                        const newReferralHtml = `
                                            <div class="bg-dark-2 p-4 rounded-lg mb-2">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <p class="text-sm text-gray-400">${data.referral.referred_email}</p>
                                                        <p class="text-yellow-500">€${data.referral.amount}</p>
                                                    </div>
                                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-500 text-dark-1">
                                                        {{ __('Pending') }}
                                                    </span>
                                                </div>
                                            </div>
                                        `;
                                        referralsList.insertAdjacentHTML('afterbegin', newReferralHtml);
                                    }
                                }
                            } else {
                                showModal('{{ __("Error") }}', data.message);
                            }
                        } catch (error) {
                            hideLoading();
                            console.error('Error:', error);
                            showModal('{{ __("Error") }}', '{{ __("An error occurred. Please try again later.") }}');
                        }
                    });
                });
            </script>
    
            <style>
                .loading-overlay {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.7);
                    z-index: 9999;
                    justify-content: center;
                    align-items: center;
                }
    
                .loading-gif {
                    width: 100px;
                    height: 100px;
                }
            </style>
        @endpush
    </div>

</x-app-layout>
