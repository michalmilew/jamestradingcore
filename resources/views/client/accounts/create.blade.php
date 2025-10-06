<x-app-layout>
    <x-slot name="header">
        <h2>
            {{__('Add MetaTrader 4 Account')}}
        </h2>
    </x-slot>
    <div class="p-5">
        <div class="max-w-7x1 mx-auto ">
            <div class=" ">
                <div class="overflow-x-auto justify-between rounded-lg px-2 py-2">
                    <div class="flex flex-col">
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    <form id="metacreator-form" method="POST" action="{{ route(\App\Models\SettingLocal::getLang().'.client.accounts.store') }}">
                    @csrf
                    <table >
                            <tr class =" hidden"><td>
                                <x-label for="type" :value="__('Type')" /></td><td>
                                <select name="type" id="type">
                                    <option value="1">{{__('Master')}}</option>
                                </select>
                            </td></tr>
                            <tr class ="hidden"><td>
                                <x-label for="name" :value="__('Custom account name')" /></td><td>
                                <input type="text" name="name" id="name">
                            </td></tr>
                            <tr class ="hidden"><td>
                                <x-label for="broker" :value="__('Broker')" /></td><td>
                                <select id="broker" name="broker" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="mt4">MT4</option>
                                </select>
                            </td></tr>
                            <tr  class="flex md:table-row flex-col"><td>
                                <x-label for="login" :value="__('Account')" /></td><td>
                                <input type="number" name="login" id="login">
                            </td></tr>
                            <tr  class="flex md:table-row flex-col"><td>
                                <x-label for="password" :value="__('Password')" /></td><td>
                                <input type="password" name="password" id="password">
                            </td></tr>
                            <tr  class="flex md:table-row flex-col"><td>
                                <x-label for="server" :value="__('Server')" /></td><td>
                                @if(auth()->user()->broker !== 'Other')
                                    <select id="server" name="server" class="px-2 block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        @foreach($servers as $server)
                                        <option value="{{$server->name}}" >{{$server->name}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" name="server" id="server">
                                @endif
                            </td></tr>
                            <tr class ="hidden"><td>
                                <x-label for="environment" :value="__('Environment')" /></td><td>
                                <select name="environment" id="environment">
                                <option value="Real">Real</option>
                                </select>
                            </td></tr>
                            <tr class ="hidden"><td>
                                <x-label for="status" :value="__('Status')" /></td><td>
                                <select name="status" id="status">
                                <option value="1">{{__('Enabled')}}</option>
                                </select>
                            </td></tr>
                            <tr class="flex md:table-row flex-col"><td>
                                <x-label for="group" :value="__('Setting')" /></td><td>
                                <select name="groupid" id="groupid" class="px-2 block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @if(auth()->user()->broker !== 'Other')
                                        @foreach($enabledRiskSettings as $riskSetting)
                                            @if(auth()->user()->is_vip >= 4 && $riskSetting->name == 'pro+++')
                                                <option value="{{ $riskSetting->value }}">{{ __(ucfirst($riskSetting->name)) }}</option>
                                            @elseif(auth()->user()->is_vip >= 3 && $riskSetting->name == 'pro++')
                                                <option value="{{ $riskSetting->value }}">{{ __(ucfirst($riskSetting->name)) }}</option>
                                            @elseif(auth()->user()->is_vip >= 2 && $riskSetting->name == 'pro+')
                                                <option value="{{ $riskSetting->value }}">{{ __(ucfirst($riskSetting->name)) }}</option>
                                            @elseif(auth()->user()->is_vip >= 1 && $riskSetting->name == 'pro')
                                                <option value="{{ $riskSetting->value }}">{{ __(ucfirst($riskSetting->name)) }}</option>
                                            @elseif(in_array($riskSetting->name, ['high', 'medium', 'low']))
                                                <option value="{{ $riskSetting->value }}">{{ __(ucfirst($riskSetting->name)) }}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option value="EVZiiLZp">{{ __(ucfirst('Low')) }}</option>
                                        <option value="LZZiiLZp">{{ __(ucfirst('High')) }}</option>
                                    @endif
                                </select>
                            </td></tr>
                            <tr class ="hidden"><td>
                                <x-label for="subscription" :value="__('Subscription')" /></td><td>
                                <input type="text" name="subscription" id="subscription" value="auto">
                            </td></tr>
                            <tr class =" hidden"><td>
                                <x-label for="pending" :value="__('Copy pending order')" /></td><td>
                                <select name="pending" id="pending">
                                <option value="1">{{__('Enabled')}}</option>
                                </select>
                            </td></tr>
                            <tr class ="hidden"><td>
                                <x-label for="stop_loss" :value="__('Copy StopLoss')" /></td><td>
                                <select name="stop_loss" id="stop_loss">
                                <option value="1">{{__('Enabled')}}</option>
                                </select>
                            </td></tr>
                            <tr class ="hidden"><td>
                                <x-label for="take_profit" :value="__('Copy TakeProfit')" /></td><td>
                                <select name="take_profit" id="take_profit">
                                <option value="1">{{__('Enabled')}}</option>
                                </select>
                            </td></tr>
                            <tr class ="hidden"><td>
                                <x-label for="comment" :value="__('Comment')" /></td><td>
                                <input type="text" name="comment" id="comment">
                            </td></tr>
                            <tr class ="hidden"><td>
                                <x-label for="alert_email" :value="__('Send warning email')" /></td><td>
                                <select name="alert_email" id="alert_email">
                                <option value="0">{{__('Disabled')}}</option>
                                </select>
                            </td></tr>
                            <tr class ="hidden"><td>
                                <x-label for="alert_sms" :value="__('Send warning sms')" /></td><td>
                                <select name="alert_sms" id="alert_sms">
                                <option value="0">{{__('Disabled')}}</option>
                                </select>
                            </td></tr>

                            <tr class ="right">
                                <td colspan="2">
                                    <x-button class="ml-3 mt-4">
                                    {{ __('Add') }} {{ __('Account') }}
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
        /* Hide number input spinners */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield; /* Firefox */
        }

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
            width: 100px;
            height: 100px;
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