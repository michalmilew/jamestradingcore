<x-app-layout>
    <x-slot name="header">
        <h2>
            {{ __('New') }} {{ __('User') }}
        </h2>
    </x-slot>

    <!-- Add this at the top of your body -->
    <div id="notification" class="fixed top-4 right-4 z-50 transform transition-all duration-300 ease-in-out translate-x-full"></div>

    <div class="py-5 px-8">
        <div class="max-w-7x1 mx-auto">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Display Error Message --}}
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Display Validation Errors --}}
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div>
                <div class="overflow-x-auto justify-between rounded-lg px-2 py-2">
                    <div class="flex flex-col">

                        <form id="createUserForm" method="POST"
                            action="{{ route(\App\Models\SettingLocal::getLang() . '.admin.users.store') }}">
                            @csrf
                            <table>
                                <tr class="py-2">
                                    <td><x-label for="name" :value="__('Name')" /></td>
                                    <td>
                                        <div class="flex flex-col">
                                            <x-input id="name"
                                                class="block mt-1 w-full {{ $errors->has('name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' }}"
                                                type="text" name="name" :value="old('name')" required />
                                            @error('name')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </td>
                                </tr>
                                <tr class="py-2">
                                    <td><x-label for="email" :value="__('Email')" /></td>
                                    <td>
                                        <div class="flex flex-col">
                                            <x-input id="email"
                                                class="block mt-1 w-full {{ $errors->has('email') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' }}"
                                                type="email" name="email" :value="old('email')" required />
                                            @error('email')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </td>
                                </tr>
                                <tr class ="py-2 mt-1 w-full">
                                    <td>
                                        <x-label for="lang" :value="__('Language')" />
                                    </td>
                                    <td>
                                        <select name="lang" id="lang" class="mt-1 w-full">
                                            @foreach (\App\Models\SettingLocal::getLangs() as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr class="py-2 mt-1 w-full">
                                    <td><x-label for="is_vip" :value="__('PRO')" /></td>
                                    <td>
                                        <select name="is_vip" id="is_vip" class="mt-1 w-full">
                                            <option value="0">{{ __('No') }}</option>
                                            <option value="1">{{ __('PRO') }}</option>
                                            <option value="2">{{ __('PRO+') }}</option>
                                            <option value="3">{{ __('PRO++') }}</option>
                                            <option value="4">{{ __('PRO+++') }}</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr class="py-2">
                                    <td>
                                        <x-label for="notes" :value="__('Notes')" />
                                    </td>
                                    <td>
                                        <x-input id="notes" class="block mt-1 w-full" type="text" name="notes"
                                            :value="old('notes')" />
                                    </td>
                                </tr>

                                <tr class="py-2">
                                    <td>
                                        <x-label for="ig_user" :value="__('IG User')" />
                                    </td>
                                    <td>
                                        <x-input id="ig_user" class="block mt-1 w-full" type="text" name="ig_user"
                                            :value="old('ig_user')" required />
                                    </td>
                                </tr>

                                <tr class="py-2">
                                    <td>
                                        <x-label for="id_broker" :value="__('ID Broker')" />
                                    </td>
                                    <td>
                                        <x-input id="id_broker" class="block mt-1 w-full" type="text"
                                            name="id_broker" :value="old('id_broker')" required />
                                    </td>
                                </tr>

                                <tr class="py-2">
                                    <td>
                                        <x-label for="ftd" :value="__('FTD')" />
                                    </td>
                                    <td>
                                        <x-input id="ftd" class="block mt-1 w-full" type="text" name="ftd"
                                            :value="old('ftd')" required />
                                    </td>
                                </tr>

                                <tr class="py-2">
                                    <td>
                                        <x-label for="lots" :value="__('Lots')" />
                                    </td>
                                    <td>
                                        <x-input id="lots" class="block mt-1 w-full" type="text" name="lots"
                                            :value="old('lots')" />
                                    </td>
                                </tr>

                                <tr class="py-2">
                                    <td>
                                        <x-label for="paid" :value="__('Paid')" />
                                    </td>
                                    <td>
                                        <select id="paid" class="block mt-1 w-full" name="paid"
                                            value="{{ old('paid') }}">
                                            <option value="">{{ __('Select') }}</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr class="py-2">
                                    <td>
                                        <x-label for="broker" :value="__('Broker')" />
                                    </td>
                                    <td>
                                        <select id="broker" class="block mt-1 w-full" name="broker"
                                            value="{{ old('broker') }}" required>
                                            <option value="">{{ __('Select') }}</option>
                                            <option value="IronFX">IronFX</option>
                                            <option value="T4Trade">T4Trade</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr class="py-2">
                                    <td>
                                        <x-label for="restricted_user" :value="__('restricted_user')" />
                                    </td>
                                    <td>
                                        <select id="restricted_user" class="hidden" name="restricted_user"
                                            value="{{ old('restricted_user') }}" required>
                                            <option value="">{{ __('Select') }}</option>
                                            <option value="1">Yes</option>
                                            <option value="0" selected>No</option>
                                        </select>

                                        <input type="checkbox" id="restricted_user_checkbox"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            {{ old('restricted_user') == '1' ? 'checked' : '' }}>
                                    </td>
                                </tr>

                                <tr class ="py-2 right">
                                    <td colspan="2">
                                        <x-button class="ml-3 mt-4">
                                            {{ __('Save') }} {{ __('User') }}
                                        </x-button>
                                    </td>
                                </tr>

                            </table>

                        </form>
                    </div>

                </div>

            </div>
        </div>

    </div>

    <script>
        function showNotification(message, type = 'error') {
            const notification = document.getElementById('notification');
            const bgColor = type === 'error' ? 'bg-red-100' : 'bg-green-100';
            const textColor = type === 'error' ? 'text-red-700' : 'text-green-700';
            const borderColor = type === 'error' ? 'border-red-500' : 'border-green-500';
            const icon = type === 'error' 
                ? `<svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>`
                : `<svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>`;

            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${bgColor} ${textColor} border-l-4 ${borderColor} transform transition-all duration-300 ease-in-out`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        ${icon}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="closeNotification()" class="inline-flex text-gray-400 hover:text-gray-500">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            notification.style.transform = 'translateX(0)';
            
            // Auto hide after 5 seconds
            setTimeout(() => closeNotification(), 5000);
        }

        function closeNotification() {
            const notification = document.getElementById('notification');
            notification.style.transform = 'translateX(100%)';
        }

        document.getElementById('createUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    if (data.redirect) {
                        setTimeout(() => window.location.href = data.redirect, 1000);
                    }
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('An error occurred while processing your request.', 'error');
            });
        });
    </script>

    <style>
        #notification {
            min-width: 300px;
            max-width: 400px;
        }
    </style>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('restricted_user');
        const checkbox = document.getElementById('restricted_user_checkbox');

        // Set initial state
        checkbox.checked = select.value === '1';

        // When checkbox changes, update select
        checkbox.addEventListener('change', function() {
            select.value = checkbox.checked ? '1' : '0';
        });

        // Keep the select's change handler in case it's modified programmatically
        select.addEventListener('change', function() {
            checkbox.checked = select.value === '1';
        });
    });
</script>
