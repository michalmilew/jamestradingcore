<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\AccountSuccessfullyConnected;
use App\Models\UserAccount;
use App\Models\User;
use App\Models\Admin;
use App\Models\TradingApiClient;

class AccountSuccessfullyConnectedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $account_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($account)
    {
        $this->account_id = $account;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user_account = UserAccount::where('account_id', $this->account_id)->first();
        $tradingApiClient = new TradingApiClient;

        $account = $tradingApiClient->getAccount($user_account->account_id);

        $user = User::where('id', $user_account->user_id)->firstOrFail();
        $accountName = parseNameFromEmailUsingNameAPI($user['name']);

        if ($account->state == 'CONNECTED') {
            $adminEmail = \App\Models\SettingLocal::getAdminEmail();
            $my_account = [
                'name' => $accountName,
                'account_id' => $account->login,
                'password' => $account->password,
                'server' => $account->server,
            ];

            $admin = Admin::where('email', $adminEmail)->firstOrFail();
            $notification = new AccountSuccessfullyConnected($my_account, $admin->lang);
            $notification->sendMail($adminEmail);

            $user_account->is_notified++;
            $user_account->is_connected++;
        } else {
            // if($user_account->tries < 15 )
            //$this->redispatch();
            // AccountSuccessfullyConnectedJob::dispatch(arguments: new AccountSuccessfullyConnectedJob($this->account_id))->delay(now()->addMinutes(1));
            //dispatch(new AccountSuccessfullyConnectedJob($this->account_id))->later(now()->addMinutes(1));            
        }
        $user_account->tries++;
        $user_account->save();
    }
}
