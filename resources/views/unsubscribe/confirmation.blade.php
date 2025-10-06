<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-5 bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 ">
            <div class="overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-gray-800 border-b border-gray-700">
                    <h1 class="font-bold text-2xl mb-4 text-white">You have been unsubscribed from our mailing list.</h1>
                    <p class="text-gray-300">If this was a mistake, you can resubscribe at any time by contacting our support.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

