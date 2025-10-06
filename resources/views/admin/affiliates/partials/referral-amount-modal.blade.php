<div id="referralAmountModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-dark-1 p-6 rounded-lg w-96 border border-gray-600">
        <h3 class="text-lg font-bold mb-4 text-white">{{ __('Change Referral Amount') }}</h3>
        <form id="referralAmountForm">
            <div class="mb-4">
                <label class="block mb-2 text-gray-300">{{ __('Amount per Referral') }}</label>
                <input type="number" name="amount" step="0.01" min="0" required 
                    class="w-full rounded-lg bg-dark-2 border-gray-600 focus:border-green-500 focus:ring-green-500 text-gray-300">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeReferralAmountModal()" 
                    class="px-4 py-2 bg-dark-2 text-white rounded-lg border border-gray-600 hover:border-red-500 transition-colors duration-200">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" 
                    class="px-4 py-2 bg-green-perso text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                    {{ __('Save') }}
                </button>
            </div>
        </form>
    </div>
</div> 