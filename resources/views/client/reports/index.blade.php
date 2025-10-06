<x-app-layout>
    <x-slot name="header">
        <h2 class="semibold text-xl">
            {{ __('dashboard.report-title') }}
        </h2>
    </x-slot>

    <div class="p-5">
        <div class="mx-auto">
            <div class="overflow-hidden shadow-sm sm:rounded-lg">
                @isset($error)
                    <div>
                        <div class="font-medium text-red-600">
                            {{ $error }}
                        </div>
                    </div>
                @endisset
                @if (auth()->user()->userAccounts->count() > 0)
                    @isset($total_data)
                        <div class="overflow-x-auto rounded-lg mt-2">
                            <div class="font-bold mb-2">{{ __('dashboard.Pnl') }}: € {{ $total_data['pnl'] }}</div>
                        </div>
                    @endisset
                    <div class="overflow-x-auto rounded-lg mt-2 flex md:flex-row flex-col justify-between gap-2.5">
                        <div class="w-full display flex-col gap-2.5">
                            <div class="w-full h-[400px]">
                                @if (!empty($pnl_arr))
                                    <canvas id="pnlChart"></canvas>
                                @else
                                    <p>No PnL data available for this year.</p>
                                @endif
                            </div>
                            <div style="text-align: center; margin-top: 10px;">
                                <strong>{{ __('dashboard.pnl-chart') }}</strong>
                            </div>
                        </div>
                        <div class="w-full display flex-col gap-2.5">
                            <div class="w-full h-[400px]">
                                @if (!empty($balance_arr))
                                    <canvas id="balanceChart"></canvas>
                                @else
                                    <p>No Balance data available for this year.</p>
                                @endif
                            </div>
                            <div style="text-align: center; margin-top: 10px;">
                                <strong>{{ __('dashboard.balance-chart') }}</strong>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-red-500">
                        {{ __('Without any MetaTrader 4 account connected to the platform') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Function to adjust font size based on screen width
        function getFontSize() {
            return window.innerWidth < 768 ? 10 : 14; // Adjust sizes for mobile and desktop
        }

        @if (!empty($pnl_arr))
            var ctx = document.getElementById('pnlChart').getContext('2d');
            var pnlChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Profit and Loss',
                        data: @json($pnl_arr),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Allows the chart to resize freely
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    size: getFontSize() // Dynamic font size
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: getFontSize() // Dynamic font size
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                font: {
                                    size: getFontSize() // Dynamic font size for legend
                                }
                            }
                        }
                    }
                }
            });
        @endif

        @if (!empty($balance_arr))
            var ctx_ = document.getElementById('balanceChart').getContext('2d');
            var balanceChart = new Chart(ctx_, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Balance',
                        data: @json($balance_arr),
                        borderColor: 'rgba(75, 192, 75, 1)', // Darker green for the border
                        backgroundColor: 'rgba(75, 192, 75, 0.2)', // Lighter green for the background
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Allows the chart to resize freely
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    size: getFontSize() // Dynamic font size
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: getFontSize() // Dynamic font size
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                font: {
                                    size: getFontSize() // Dynamic font size for legend
                                }
                            }
                        }
                    }
                }
            });
        @endif
    </script>
</x-app-layout>
