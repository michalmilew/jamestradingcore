<x-app-layout>
    <x-slot name="header">
        <h2>
            {{__('MetaTrader 4 Account')}}
        </h2>
    </x-slot>
    <div class="p-5">
        <div class="max-w-7x1 mx-auto px-8">
            <div class=" ">
                <div class="overflow-x-auto justify-between rounded-lg px-2 py-2">
                    <div class="flex justify-between">
                        @if (auth()->user()->userAccounts->count() < 1 || (count($accounts) == 0 && !isset($error)))
                            <a href="{{ route(\App\Models\SettingLocal::getLang() . '.client.accounts.create') }}"
                                class="flex right items-center bg-green-perso hover:bg-green-perso text-whita font-bold py-2 px-4 rounded-lg text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="16"></line>
                                    <line x1="8" y1="12" x2="16" y2="12"></line>
                                </svg>
                                {{ __('Add') }} {{ __('Account') }}
                            </a>
                        @endif
                    </div>

                </div>

                <div class="overflow-x-auto text-white rounded-lg px-2 mt-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <div class="col-span-1 hidden md:col-span-1 md:flex">{{ __('Email') }}</div>
                                <div class="col-span-1 hidden md:col-span-1 md:flex">{{ __('Balance') }}</div>
                                <div class="col-span-1 hidden md:col-span-1 md:flex">{{ __('Setting') }}</div>
                                <div class="col-span-1 hidden md:col-span-1 md:flex">{{ __('State') }}</div>
                                <div class="col-span-1 hidden md:col-span-1 md:flex">{{ __('Actions') }}</div>

                                @foreach ($accounts as $account)
                                    <div class="col-span-1 md:hidden">{{ __('Email') }} : </div>
                                    <div class="col-span-1 md:col-span-1 py-2">{{ $account->name }}</div>
                                    <div class="col-span-1 md:hidden">{{ __('Balance') }} : </div>
                                    <div class="col-span-1 md:col-span-1 py-2" id="balance_{{ $account->account_id }}">
                                        {{ $account->balance }}</div>
                                    <div class="col-span-1 md:hidden">{{ __('Setting') }} : </div>
                                    <div class="col-span-1 md:col-span-1 py-2">
                                        {{ __(App\Models\TradingGroup::groupName($account->groupid, true, auth()->user()->broker === 'Other')) }}</div>

                                    <div class="col-span-1 md:hidden">{{ __('State') }} : </div>
                                    <div class="col-span-1 md:col-span-1 py-2" id="state_{{ $account->account_id }}">
                                        {{ __($account->state) }}</div>
                                    <div class="col-span-1 md:col-span-1 flex py-2">
                                        <a href="{{ route(\App\Models\SettingLocal::getLang() . '.client.accounts.edit', $account->account_id) }}"
                                            class="bg-green-500 hover:bg-green-700 text-gray font-bold py-2 px-4  h-full rounded mx-2">{{ __('Edit') }}</a>
                                        <!-- Modal toggle -->
                                        <button data-modal-target="defaultModal" data-modal-toggle="defaultModal"
                                            class="bg-red-500 hover:bg-red-700 text-gray font-bold py-2 px-4  h-full rounded"
                                            type="button">
                                            {{ __('Delete') }}
                                        </button>
                                    </div>

                                    <!-- Main modal -->
                                    <div id="defaultModal" tabindex="-1" aria-hidden="true"
                                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                        <div class="relative w-full max-w-2xl max-h-full">
                                            <!-- Modal content -->
                                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                                <!-- Modal header -->
                                                <div
                                                    class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                                        {{ __('Confirmation') }}
                                                    </h3>
                                                    <button type="button"
                                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                                        data-modal-hide="defaultModal">
                                                        <svg class="w-3 h-3" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 14 14">
                                                            <path stroke="currentColor" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                                        </svg>
                                                        <span class="sr-only">Close modal</span>
                                                    </button>
                                                </div>
                                                <!-- Modal body -->
                                                <div class="p-6 space-y-6">
                                                    <p
                                                        class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                                        {{ __('Are you sure you want to delete this Account?') }}
                                                    </p>
                                                </div>
                                                <!-- Modal footer -->
                                                <div
                                                    class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                                                    <form id="metacreator-form"
                                                        action="{{ route(\App\Models\SettingLocal::getLang() . '.client.accounts.destroy', $account->account_id) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="bg-red-500 hover:bg-red-700 text-gray font-bold py-2 px-4  h-full rounded">{{ __('Delete') }}</button>

                                                    </form>

                                                    <button data-modal-hide="defaultModal" type="button"
                                                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Decline</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach


                            </div>
                        </div>
                    </div>

                    <div class="py-4">



                    </div>

                </div>
            </div>

        </div>
        <div class="loading-overlay" id="loading-overlay">
            <img src="{{ asset('images/loading.gif') }}" alt="Loading..." class="loading-gif">
        </div>
        <div class="alert" id="alert"></div>
        <script>
            function refreshData() {
                var accountIds = @json($accounts->pluck('account_id')->toArray());
                var langValue = @json(\App\Models\SettingLocal::getLang());
                accountIds.forEach(function(id) {
                    document.getElementById('state_' + id).innerHTML =
                        '<svg class="animate-spin h-5 w-5 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>';
                });
                setTimeout(function() {}, 1000);
                // Get the CSRF token value from the hidden input field
                var csrfToken = document.querySelector('input[name="_token"]').value;
                //var csrfToken = $('meta[name="csrf-token"]').attr('content'); // Get CSRF token value
                $.ajax({
                    url: '/' + langValue + '/accounts/refreshindex',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: csrfToken,
                        account_ids: accountIds
                    },
                    success: function(response) {
                        response.data.forEach(function(account) {
                            //console.log('status_' +account.account_id);
                            document.getElementById('state_' + account.account_id).innerHTML = account
                            .state;
                            document.getElementById('balance_' + account.account_id).innerHTML = account
                                .balance;
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }

            // Call the named function when needed
            $(document).ready(function() {
                setInterval(refreshData, 10000); // Call every 5 seconds
            });

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
                            window.location.href = "{{ url(\App\Models\SettingLocal::getLang() . '/accounts') }}";
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
    </div>
</x-app-layout>
