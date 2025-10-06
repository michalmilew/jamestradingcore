<?php

namespace App\Jobs;

use App\Models\TradingApiClient;
use App\Notifications\YourAccountIsDisConnected;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class HandleDisconnectedAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $account;
    protected $user_account;

    /**
     * Create a new job instance.
     *
     * @param $account
     * @param $user_account
     */
    public function __construct($account, $user_account)
    {
        $this->account = $account;
        $this->user_account = $user_account;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // try {
        //     $tradingApiClient = new TradingApiClient;
        //     $accounts = $tradingApiClient->getAccounts();

        //     $accounts = collect($accounts);

        //     foreach ($accounts as $updatedAccount) {
        //         if($updatedAccount->account_id === $this->account->account_id) {
        //             $lastKnownState = $this->account->last_known_state ?? 'UNKNOWN'; 

        //             if ($updatedAccount->state == 'DISCONNECTED' && $lastKnownState != $updatedAccount->state) {
        //                 $my_account = [
        //                     'name' => $this->account->name,
        //                     'account_id' => $this->account->login,
        //                     'password' => $this->account->password,
        //                     'server' => $this->account->server,
        //                     'lang' => ($this->user_account->user != null) ? ($this->user_account->user->lang ?? 'en') : 'en'
        //                 ];
        
        //                 Log::channel('command')->info('Preparing to send Disconnect email...');
        
        //                 $notification = new YourAccountIsDisConnected($my_account, $this->account->lang);
        //                 $res = $notification->sendMail($this->account->name);
        
        //                 if ($res === null) {
        //                     Log::channel('command')->info('Failed to send the disconnection mail to: ' . $this->account->name);
        //                 } else {
        //                     // Update last known state to DISCONNECTED
        //                     $this->user_account->last_known_state = 'DISCONNECTED';
        //                     $this->user_account->save();
        
        //                     Log::channel('command')->info('Sent the disconnection mail to: ' . $this->account->name);
        //                 }
        //             } else {
        //                 // If the account is no longer disconnected, log and exit
        //                 Log::channel('command')->info('Account ' . $this->account->name . ' is no longer disconnected. No email sent.');
        //             }
        //         }
        //     }
            
        // } catch (\Throwable $th) {
        //     Log::channel('command')->info('Error in checking account state: ' . $th->getMessage());
        // }
        $this->user_account->last_known_state = 'DISCONNECTED';
        $this->user_account->save();
    }
}
