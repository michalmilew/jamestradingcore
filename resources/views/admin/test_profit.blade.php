<x-app-layout>
    <x-slot name="header">
        <h2 class="semibold text-xl">
            {{ __('Test Profit Email') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-6 ">
            <div class="bg-dark-2 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-dark-2 border-b border-gray-800">
                    <form action="{{ route('admin.testprofit.send') }}" method="POST" class="bg-dark-2 p-4 rounded-lg">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="price" class="block text-gray-100">{{ __('Price') }}:</label>
                            <input type="number" id="price" name="price" class="w-full px-3 py-2 border border-gray-600 text-gray-100" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="pnl" class="block text-gray-100">{{ __('PNL Value') }}:</label>
                            <input type="number" id="pnl" name="pnl" class="w-full px-3 py-2 border border-gray-600 text-gray-100" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="email" class="block text-gray-100">{{ __('Email') }}:</label>
                            <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-600 text-gray-100" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="language" class="block text-gray-100">{{ __('Language') }}:</label>
                            <select id="language" name="language" class="w-full px-3 py-2 border border-gray-600 text-gray-100" required>
                                <option value="en">{{ __('English') }}</option>
                                <option value="pt">{{ __('Portuguese') }}</option>
                                <option value="es">{{ __('Spanish') }}</option>
                                <option value="de">{{ __('German') }}</option>
                                <option value="nl">{{ __('Dutch') }}</option>
                                <option value="it">{{ __('Italian') }}</option>
                                <option value="fr">{{ __('French') }}</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-green-perso hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Send Test Profit Email') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
