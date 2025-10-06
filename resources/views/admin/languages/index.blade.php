<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Language Files') }}</h2>
    </x-slot>

    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-semibold mb-6">Select a Language to Edit</h2>
    
        {{-- Display Success Message --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
    
        {{-- Display Error Message --}}
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Language Dropdown and Edit Button -->
        <div class="flex items-center space-x-4">
            <select id="language-select" class="form-select block w-full mt-1">
                @foreach($locales as $locale)
                    <option value="{{ route(\App\Models\SettingLocal::getLang() . '.admin.languages.edit', $locale) }}">
                        {{ strtoupper($locale) }}
                    </option>
                @endforeach
            </select>
            <button id="edit-button" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Edit
            </button>
        </div>
    </div>

    <!-- JavaScript to handle dropdown selection and redirection -->
    <script>
        document.getElementById('edit-button').addEventListener('click', function() {
            var select = document.getElementById('language-select');
            var url = select.value;
            if (url) {
                window.location.href = url;
            }
        });
    </script>
</x-app-layout>
