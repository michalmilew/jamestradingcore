<!-- Referral History Modal -->
<div id="referralHistoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-dark-1 rounded-lg p-6 max-w-4xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-white">{{ __('Referral History') }}</h3>
            <button onclick="closeReferralHistoryModal()" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-dark-2 rounded-lg p-4">
                    <h4 class="text-lg font-semibold mb-2">{{ __('Amount pending payment') }}</h4>
                    <p class="text-2xl font-bold text-yellow-500" id="pendingAmount">€0.00</p>
                </div>
                <div class="bg-dark-2 rounded-lg p-4">
                    <h4 class="text-lg font-semibold mb-2">{{ __('Total amount already paid') }}</h4>
                    <p class="text-2xl font-bold text-green-500" id="paidAmount">€0.00</p>
                </div>
            </div>

            <div class="referrals-list space-y-2" id="referralsList">
                <!-- Referrals will be loaded here dynamically -->
            </div>
        </div>
    </div>
</div>

<script>
function openReferralHistoryModal(userId) {
    const modal = document.getElementById('referralHistoryModal');
    modal.style.display = 'flex';
    
    // Show loading state
    document.getElementById('referralsList').innerHTML = '<div class="text-center py-4">Loading...</div>';
    
    // Fetch referral history using the route helper
    fetch(`{{ url('admin/affiliates') }}/${userId}/referrals`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update amounts
            document.getElementById('pendingAmount').textContent = `€${parseFloat(data.pendingAmount).toFixed(2)}`;
            document.getElementById('paidAmount').textContent = `€${parseFloat(data.paidAmount).toFixed(2)}`;
            
            // Update referrals list
            const referralsList = document.getElementById('referralsList');
            if (data.referrals && data.referrals.length > 0) {
                referralsList.innerHTML = data.referrals.map(referral => `
                    <div class="bg-dark-2 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-400">${referral.referred_email}</p>
                                <p class="text-${referral.status === 'pending' ? 'yellow' : 'green'}-500">
                                    €${parseFloat(referral.amount).toFixed(2)}
                                </p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full ${referral.status === 'pending' ? 'bg-yellow-500' : 'bg-green-500'} text-dark-1">
                                ${referral.status === 'pending' ? '{{ __("Pending") }}' : '{{ __("Paid") }}'}
                            </span>
                        </div>
                    </div>
                `).join('');
            } else {
                referralsList.innerHTML = '<div class="text-center py-4 text-gray-400">{{ __("No referrals found") }}</div>';
            }
        } else {
            throw new Error(data.message || '{{ __("Failed to load referral history") }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('referralsList').innerHTML = `
            <div class="text-center py-4 text-red-500">
                {{ __("An error occurred while loading referral history.") }}
                <br>
                <small>${error.message}</small>
            </div>
        `;
    });
}

function closeReferralHistoryModal() {
    const modal = document.getElementById('referralHistoryModal');
    modal.style.display = 'none';
}
</script> 