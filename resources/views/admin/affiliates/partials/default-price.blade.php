<div class="mb-6 bg-dark-2 p-4 rounded-lg">
    <h3 class="text-lg font-bold mb-4 text-white">{{ __('Default Referral Price') }}</h3>
    <div class="flex flex-col md:flex-row itesm-start md:items-center gap-4">
        @php
            $defaultPrice = \App\Models\Setting::where('key', 'default_referral_price')->first();
        @endphp
        <input type="number" id="defaultReferralPrice" 
            value="{{ $defaultPrice ? $defaultPrice->value : 0 }}" 
            step="0.01" min="0" 
            class="rounded-lg bg-dark-1 border-gray-600 focus:border-green-500 focus:ring-green-500 text-gray-300">
        <button onclick="updateDefaultPrice()" 
            class="px-4 py-2 bg-green-perso text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
            {{ __('Update Default Price') }}
        </button>
    </div>
</div> 