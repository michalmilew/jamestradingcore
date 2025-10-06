<div class="flex flex-wrap gap-2 items-center">
    <button onclick="openPaymentModal({{ $affiliate->id }})" 
        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 text-sm">
        {{ __('Pay') }}
    </button>
    <button onclick="openReferralAmountModal({{ $affiliate->id }})" 
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm">
        {{ __('Change Rate') }}
    </button>
    <button onclick="openReferralHistoryModal({{ $affiliate->id }})" 
        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 text-sm">
        {{ __('View History') }}
    </button>
</div> 