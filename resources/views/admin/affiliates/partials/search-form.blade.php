<div class="mb-6">
    <form method="GET" action="{{ route(app()->getLocale() . '.admin.affiliates.index') }}">
        <div class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" 
                placeholder="{{ __('Search by name or email...') }}" 
                class="w-full sm:w-96 rounded-lg bg-dark-2 border-gray-600 focus:border-green-500 focus:ring-green-500 text-gray-300">
            <button type="submit" 
                class="px-6 py-2 bg-green-perso text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                {{ __('Search') }}
            </button>
        </div>
    </form>
</div> 