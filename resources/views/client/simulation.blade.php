<x-app-layout>
    <x-slot name="header">
        <h2 class="semibold text-xl md:text-2xl">
            {{ __('Calculate your potential profits') }}
        </h2>
    </x-slot>

    <div class="p-4 md:p-12">
        <div class="mx-auto sm:px-6 col-span-12 grid grid-cols-12 items-center">
            <div class="col-span-12 md:col-span-6">
                <div class="flex flex-col gap-4">
                    <p class="text-base md:text-lg">
                        {{ __('The James Trading Group comes with different settings to choose from, Low, Medium, and High for accounts with a deposit of less than 2000€.') }}
                    </p>
                    <p class="text-base md:text-lg pb-2">
                        {{ __('Accounts with more than 2000€ deposit can have the PRO setting, which has the following levels of settings for PRO accounts:') }}
                    </p>
                    <p class="text-base md:text-lg pb-2">
                        @foreach ($riskSettings as $level)
                            @if (strpos($level->name, 'pro') === 0)
                                {{ strtoupper($level->name) }}: {{ __('PRO-Calc') }}
                                {{ number_format($level->min_deposit, 0, '.', ',') }}€<br>
                            @endif
                        @endforeach
                    </p>
                    <p class="text-base md:text-lg pb-2">
                        {{ __('When making the deposit to have one of the PRO settings send a message to add your account to the PRO level settings.') }}
                    </p>
                    <p class="text-base md:text-lg pb-2">
                        {{ __('Here you can calculate your potential profits with it, depending on your initial deposit size, risk setting, and time period') }}:
                    </p>
                </div>
            </div>

            <div class="col-span-12 md:col-span-6">
                <form class="max-w-md mx-auto py-6">
                    <div class="mb-4">
                        <label for="deposit" class="block mb-2 text-base md:text-lg">{{ __('Deposit') }}:</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-base md:text-lg">€</span>
                            <input type="number" id="deposit" name="deposit"
                                class="border border-gray-300 pl-8 px-6 w-full text-base md:text-lg"
                                style="padding-left: 30px;" placeholder="{{ __('Enter deposit amount') }}"
                                oninput="updateTotal()" required>
                            <p class="text-red-700" id="minValueMessage"></p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-base md:text-lg">{{ __('Setting') }}:</label>

                        {{-- Label for Normal Users --}}
                        <p class="text-sm md:text-base font-semibold mb-2">{{ __('Normal Users') }}</p>

                        <div class="flex flex-wrap gap-4 mb-4">
                            {{-- Basic Settings: Low, Medium, High --}}
                            @foreach ($riskSettings as $setting)
                                @if (in_array($setting->name, ['low', 'medium', 'high']))
                                    <div class="flex items-center">
                                        <input type="radio" id="{{ $setting->name }}-risk" name="risk"
                                            value="{{ $setting->name }}" class="mr-2" onclick="updateTotal()"
                                            {{ $setting->enabled ? '' : 'disabled' }}
                                            {{ $setting->name === 'low' ? 'checked' : '' }} data-min-deposit="{{ $level->min_deposit }}">
                                        <label for="{{ $setting->name }}-risk"
                                            class="text-base md:text-lg {{ $setting->enabled ? '' : 'text-gray-500 cursor-not-allowed' }}">
                                            {{ __(ucfirst($setting->name)) }}
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        {{-- Label for PRO Users --}}
                        <p class="text-sm md:text-base font-semibold mb-2">{{ __('PRO Users') }}</p>

                        <div class="flex flex-wrap gap-4">
                            {{-- PRO Settings: PRO, PRO+, PRO++, PRO+++ --}}
                            @foreach ($riskSettings as $setting)
                                @if (in_array($setting->name, ['pro', 'pro+', 'pro++', 'pro+++']))
                                    <div class="flex items-center">
                                        <input type="radio" id="{{ $setting->name }}-risk" name="risk"
                                            value="{{ $setting->name }}" class="mr-2" onclick="updateTotal()"
                                            {{ $setting->enabled ? '' : 'disabled' }}>
                                        <label for="{{ $setting->name }}-risk"
                                            class="text-base md:text-lg {{ $setting->enabled ? '' : 'text-gray-500 cursor-not-allowed' }}">
                                            {{ __(ucfirst($setting->name)) }}
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="time-period"
                            class="block mb-2 text-base md:text-lg">{{ __('Time Period') }}:</label>
                        <input type="range" id="time-period" name="timePeriod" min="1" max="36"
                            step="1" class="w-full" oninput="updateTotal()" required>
                        <div class="flex justify-between items-center">
                            <span>1 {{ __('month') }}</span>
                            <output name="selectedPeriod" id="selectedPeriod"
                                class="block mt-2 text-center font-semibold text-white"></output>
                            <span>36 {{ __('months') }}</span>
                        </div>
                    </div>

                    <div class="mb-4 rounded-lg border-t bg-green-900 border-b border-green-400 p-4 mt-6">
                        <label for="total-profits"
                            class="block mb-2 text-base md:text-lg">{{ __('Total Potential Profits') }}:</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-base md:text-lg">€</span>
                            <input type="text" id="total-profits" name="totalProfits"
                                class="border border-gray-300 p-2 w-full text-base md:text-lg"
                                style="padding-left: 30px;" readonly>
                        </div>
                        <p class="text-sm mt-2 text-gray-300 italic">
                            {{ __('This simulation is for illustrative purposes only and does not guarantee future performance.') }}
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set default values
            document.getElementById('time-period').value = 1;
            document.getElementById('selectedPeriod').textContent = '1 {{ __('month') }}';
            document.getElementById('deposit').value = 0;

            // Ensure the default risk setting is selected
            const defaultRiskSetting = document.querySelector('input[name="risk"][value="low"]');
            if (defaultRiskSetting) {
                defaultRiskSetting.checked = true;
            }

            // Trigger initial calculation
            updateTotal();
        });

        function updateTotal() {
            const timePeriod = document.getElementById('time-period').value;
            const deposit = document.getElementById('deposit').value;
            const riskSetting = document.querySelector('input[name="risk"]:checked');
            const totalProfits = document.getElementById('total-profits');
            const selectedPeriod = document.getElementById('selectedPeriod');
            const depositInput = document.getElementById('deposit');
            const minValueMessage = document.getElementById("minValueMessage");

            if (!riskSetting) {
                totalProfits.value = '';
                selectedPeriod.textContent = '';
                return;
            }

            const riskValue = riskSetting.value;

            let multiplier;
            let minValue = 0;
            @foreach ($riskSettings as $setting)
                if (riskValue === '{{ $setting->name }}') {
                    multiplier = {{ $setting->multiplier }};
                    minValue = {{ $setting->min_deposit }};
                }
            @endforeach

            depositInput.setAttribute("min", minValue);
            if (deposit < minValue) {
                minValueMessage.textContent = "{{ __('Minimum value is :') }}" + `${minValue}€`;
            } else {
                minValueMessage.textContent = ``;
            }

            const total = timePeriod * deposit * multiplier;
            totalProfits.value = total.toFixed(2);
            selectedPeriod.textContent = timePeriod + (timePeriod > 1 ? " {{ __('months') }}" : " {{ __('month') }}");
        }
    </script>
</x-app-layout>
