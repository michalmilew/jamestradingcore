<x-app-layout>
    <x-slot name="header">
        <h2>
            {{ __('Show') }} {{ __('Account') }}
        </h2>
    </x-slot>
    <div class="p-5">
        <div class="max-w-7x1 mx-auto ">
            <div class=" ">
                <div class="overflow-x-auto justify-between  rounded-lg px-2 py-2">
                    <div class="flex flex-col">
                        <table>
                            <tr class="border-b px-2">
                                <td>Account ID </td>
                                <td>{{ $account->account_id }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Type </td>
                                <td>{{ $account->type == '0' ? 'Master' : 'Slave' }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Name </td>
                                <td>{{ $account->name }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Broker </td>
                                <td>{{ $account->broker }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Login </td>
                                <td>{{ $account->login }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Account </td>
                                <td>{{ $account->account }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Password </td>
                                <td>{{ $account->password }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Server </td>
                                <td>{{ $account->server }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Environment </td>
                                <td>{{ $account->environment }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Status </td>
                                <td>{{ $account->status == '1' ? 'enabled' : 'desabled' }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>State </td>
                                <td>{{ $account->state }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Group ID </td>
                                <td>{{ $account->groupid }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Subscription Key </td>
                                <td>{{ $account->subscription_key }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Subscription Name </td>
                                <td>{{ $account->subscription_name }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Expiration </td>
                                <td>{{ $account->expiration }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Pending </td>
                                <td>{{ $account->pending == '1' ? 'enabled' : 'desabled' }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Stop Loss </td>
                                <td>{{ $account->stop_loss == '1' ? 'enabled' : 'desabled' }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Take Profit </td>
                                <td>{{ $account->take_profit == '1' ? 'enabled' : 'desabled' }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Comment </td>
                                <td>{{ $account->comment }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Alert Email </td>
                                <td>{{ $account->alert_email == '1' ? 'enabled' : 'desabled' }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Alert SMS </td>
                                <td>{{ $account->alert_sms == '1' ? 'enabled' : 'desabled' }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Alert Email Failed </td>
                                <td>{{ $account->alert_email_failed == '1' ? 'enabled' : 'desabled' }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Alert SMS Failed </td>
                                <td>{{ $account->alert_sms_failed == '1' ? 'enabled' : 'desabled' }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Global Stop Loss </td>
                                <td>{{ $account->globalstoploss == '1' ? 'enabled' : 'desabled' }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Global Stop Loss Type</td>
                                <td>{{ $account->globalstoplosstype == '0' ? 'Close Only' : ($account->globalstoplosstype == '1' ? 'Sell Out' : ($account->globalstoplosstype == '2' ? 'Frozen' : '')) }}
                                </td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Global Stop Loss Value </td>
                                <td>{{ $account->globalstoplossvalue }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Global Take Profit </td>
                                <td>{{ $account->globatakeprofit == '1' ? 'enabled' : 'desabled' }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Global Take Profit Value </td>
                                <td>{{ $account->globaltakeprofitvalue }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Global Take Profit Type </td>
                                <td>{{ $account->globaltakeprofittype }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Balance </td>
                                <td>{{ $account->balance }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Equity </td>
                                <td>{{ $account->equity }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Free Margin </td>
                                <td>{{ $account->free_margin }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Credit </td>
                                <td>{{ $account->credit }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Account currency </td>
                                <td>{{ $account->ccy }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Accounting type</td>
                                <td>{{ $account->mode == '0' ? 'Hedging' : 'Netting' }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Number of open trades </td>
                                <td>{{ $account->open_trades }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Last Update </td>
                                <td>{{ $account->lastUpdate }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Access Token </td>
                                <td>{{ $account->access_token }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Refresh Token </td>
                                <td>{{ $account->refresh_token }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Expiry Token </td>
                                <td>{{ $account->expiry_token }}</td>
                            </tr>
                            <tr class="border-b px-2">
                                <td>Account </td>
                                <td>{{ $account->account }}</td>
                            </tr>
                        </table>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
