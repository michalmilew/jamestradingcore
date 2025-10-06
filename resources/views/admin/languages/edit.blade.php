<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg sm:text-xl">{{ __('Edit Language File') }} - {{ strtoupper($locale) }}</h2>
    </x-slot>

    <div class="container mx-auto p-6">
        <h2 class="text-xl sm:text-2xl font-semibold mb-6">Edit Translations for {{ strtoupper($locale) }}</h2>

        {{-- Success & Error Messages --}}
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded text-sm sm:text-base">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded text-sm sm:text-base">
                {{ session('error') }}
            </div>
        @endif

        {{-- Display Validation Errors --}}
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded text-sm sm:text-base">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Back Button --}}
        <a href="{{ route(\App\Models\SettingLocal::getLang() . '.admin.languages.index') }}"
            class="inline-flex items-center px-4 py-2 mb-6 bg-gray-300 text-gray-800 font-semibold rounded-md shadow-sm hover:bg-gray-400">
            <svg class="w-4 h-4 mr-2 text-gray-800" fill="none" stroke="currentColor"
                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Language List
        </a>

        {{-- Search Bar --}}
        <form method="GET" action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.languages.edit', $locale) }}" class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search for a key..."
                    class="w-full sm:w-2/3 p-2 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <button type="submit"
                    class="mt-2 sm:mt-0 sm:ml-2 px-4 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700">
                    Search
                </button>
                <a href="{{ route(\App\Models\SettingLocal::getLang() . '.admin.languages.edit', $locale) }}"
                    class="mt-2 sm:mt-0 sm:ml-2 px-4 py-2 bg-gray-300 text-gray-800 font-semibold rounded-md shadow-sm hover:bg-gray-400">
                    Clear
                </a>
            </div>
        </form>

        {{-- Edit Language File Form --}}
        <form action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.languages.update', $locale) }}" method="POST">
            @csrf
            @method('POST')

            {{-- Add New Translation --}}
            <div class="mb-4">
                <h3 class="text-lg sm:text-xl font-semibold mb-2">Add New Translation</h3>
                <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                    <input type="text" id="new_key" placeholder="Enter new key"
                        class="w-full sm:w-1/3 p-2 border-gray-300 rounded-md shadow-sm focus:ring-blue-500">
                    <input type="text" id="new_translation" placeholder="Enter new translation"
                        class="w-full sm:w-2/3 p-2 border-gray-300 rounded-md shadow-sm focus:ring-blue-500">
                    <button type="button" onclick="addNewTranslation()"
                        class="mt-2 sm:mt-0 px-4 py-2 bg-green-600 text-white font-semibold rounded-md shadow-sm hover:bg-green-700">
                        Add
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full bg-[#12181F] rounded-lg shadow-md">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-3 text-left font-semibold text-gray-700">Key</th>
                            <th class="p-3 text-left font-semibold text-gray-700">Translation</th>
                            <th class="p-3 text-left font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="translations-table-body">
                        @foreach ($paginator as $fullKey => $translation)
                            @php
                                $keyParts = explode('.', $fullKey, 3);
                                $type = $keyParts[0];  // json or php
                                $fileName = $keyParts[1]; // json, dashboard, auth, etc.
                                $key = $keyParts[2] ?? null; // actual translation key
                            @endphp
                            @if ($key)
                                <tr>
                                    <td class="p-3 text-white max-w-[200px]">{{ $key }}</td>
                                    <td class="p-3">
                                        <input type="text" name="translations[{{ $type }}][{{ $fileName }}][{{ $key }}]"
                                            value="{{ $translation }}"
                                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500">
                                    </td>
                                    <td class="p-3">
                                        <button type="button" onclick="removeTranslation('{{ $fullKey }}')"
                                            class="px-2 py-1 bg-red-600 text-white font-semibold rounded-md shadow-sm hover:bg-red-700">
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $paginator->appends(['search' => request('search')])->links('pagination::tailwind') }}
            </div>

            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700">
                Save Changes
            </button>
        </form>
    </div>

    {{-- JavaScript --}}
    <script>
        function removeTranslation(key) {
            const input = document.querySelector(`[name="translations[${key}]"]`);
            if (input) {
                input.setAttribute('name', `removed_translations[${key}]`);
                input.closest('tr').style.display = 'none';
            }
        }

        function addNewTranslation() {
            const newKey = document.getElementById('new_key').value.trim();
            const newTranslation = document.getElementById('new_translation').value.trim();

            if (!newKey || !newTranslation) {
                alert('Please enter both a key and a translation.');
                return;
            }

            if (document.querySelector(`[name="translations[${newKey}]"]`)) {
                alert('This key already exists.');
                return;
            }

            const tableBody = document.getElementById('translations-table-body');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td class="p-3 text-white">${newKey}</td>
                <td class="p-3">
                    <input type="text" name="translations[${newKey}]" value="${newTranslation}"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500">
                </td>
                <td class="p-3">
                    <button type="button" onclick="removeTranslation('${newKey}')"
                        class="px-2 py-1 bg-red-600 text-white font-semibold rounded-md shadow-sm hover:bg-red-700">
                        Remove
                    </button>
                </td>
            `;
            tableBody.appendChild(newRow);
        }
    </script>
</x-app-layout>
