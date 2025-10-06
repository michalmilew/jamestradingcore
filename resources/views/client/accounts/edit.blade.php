<x-app-layout>
    <x-slot name="header">
        <h2>
            {{__('Edit')}} {{__('MetaTrader 4 Account')}}
        </h2>
    </x-slot>
    <div class="p-5">
        <div class="max-w-7x1 mx-auto ">
            <div class=" ">
                <div class="overflow-x-auto justify-between  rounded-lg px-2 py-2">
                    <div class="flex flex-col">
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        <form id="metacreator-form" method="POST" action="{{ route(\App\Models\SettingLocal::getLang().'.client.accounts.update', $account->account_id) }}">
                            @csrf
                            <input type="hidden" name="account_id" value="{{ $account->account_id}}">
                            <table>
                                <tr class =" py-2">
                                    <td>
                                        <x-label for="group_id" :value="__('Setting')" /></td><td>
                                        <select name="groupid" id="groupid" class="px-2 block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            @if(auth()->user()->broker !== 'Other')
                                                @foreach($enabledRiskSettings as $riskSetting)
                                                    @if(auth()->user()->is_vip >= 4 && $riskSetting->name == 'pro+++')
                                                        <option value="{{ $riskSetting->value }}" {{ $account->groupid == $riskSetting->value ? 'selected' : '' }}>{{ __(ucfirst($riskSetting->name)) }}</option>
                                                    @elseif(auth()->user()->is_vip >= 3 && $riskSetting->name == 'pro++')
                                                        <option value="{{ $riskSetting->value }}" {{ $account->groupid == $riskSetting->value ? 'selected' : '' }}>{{ __(ucfirst($riskSetting->name)) }}</option>
                                                    @elseif(auth()->user()->is_vip >= 2 && $riskSetting->name == 'pro+')
                                                        <option value="{{ $riskSetting->value }}" {{ $account->groupid == $riskSetting->value ? 'selected' : '' }}>{{ __(ucfirst($riskSetting->name)) }}</option>
                                                    @elseif(auth()->user()->is_vip >= 1 && $riskSetting->name == 'pro')
                                                        <option value="{{ $riskSetting->value }}" {{ $account->groupid == $riskSetting->value ? 'selected' : '' }}>{{ __(ucfirst($riskSetting->name)) }}</option>
                                                    @elseif(in_array($riskSetting->name, ['high', 'medium', 'low']))
                                                        <option value="{{ $riskSetting->value }}" {{ $account->groupid == $riskSetting->value ? 'selected' : '' }}>{{ __(ucfirst($riskSetting->name)) }}</option>
                                                    @endif
                                                @endforeach
                                            @else
                                                <option value="EVZiiLZp" {{ $account->groupid == "EVZiiLZp" ? 'selected' : '' }}>{{ __(ucfirst('Low')) }}</option>
                                                <option value="LZZiiLZp" {{ $account->groupid == "LZZiiLZp" ? 'selected' : '' }}>{{ __(ucfirst('High')) }}</option>
                                            @endif
                                        </select>
                                    </td>
                                </tr>

                            <tr class =" py-2 right">
                                <td colspan="2">
                                    <x-button class="ml-3 mt-4">
                                        {{ __('Save') }} {{ __('Account') }}
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
    <div class="loading-overlay" id="loading-overlay">
        <img src="{{ asset('images/loading.gif') }}" alt="Loading..." class="loading-gif">
    </div>
    <div class="alert" id="alert"></div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('metacreator-form');
            const loadingOverlay = document.getElementById('loading-overlay');
            const alertBox = document.getElementById('alert');

            form.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent default form submission
                loadingOverlay.style.display = 'flex'; // Show the overlay

                // Create a FormData object from the form
                const formData = new FormData(form);

                // Use fetch API to submit the form data
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // To identify the request as AJAX
                    }
                })
                .then(response => response.json())
                .then(data => {
                    loadingOverlay.style.display = 'none'; // Hide the overlay
                    if (data.success) {
                        showAlert(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = "{{ url(\App\Models\SettingLocal::getLang() . '/accounts') }}";
                        }, 700); // Optional delay before redirecting
                    } else {
                        showAlert(data.message, 'error');
                    }
                })
                .catch((error) => {
                    loadingOverlay.style.display = 'none'; // Hide the overlay
                    showAlert('An unexpected error occurred.', 'error');
                });
            });

            function showAlert(message, type) {
                alertBox.textContent = message;
                alertBox.className = 'alert'; // Reset classes
                if (type === 'success') {
                    alertBox.classList.add('alert-success');
                }
                alertBox.style.display = 'block';
                setTimeout(() => {
                    alertBox.style.display = 'none';
                }, 5000); // Hide after 5 seconds
            }
        });
    </script>
    <style>
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-gif {
            width: 100px; /* Adjust the size as needed */
            height: 100px; /* Adjust the size as needed */
        }
        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            display: none;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
    </style>
</x-app-layout>

