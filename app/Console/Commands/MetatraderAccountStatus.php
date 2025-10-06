<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserAccount;
use App\Models\TradingApiClient;
use App\Notifications\AccountSuccessfullyConnected;
use App\Notifications\YourAccountIsDisConnected;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Cleanup;

class MetatraderAccountStatus extends Command
{
    protected $signature = 'email:meta-status';
    protected $description = 'Notifier admin des new connected accounts';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('"==============  Start AccountsSuccessfullyConnectedCMD "==============');
        $this->info('email:meta-status command executed at ' . now());

        $cleanup = Cleanup::firstOrCreate(
            ['id' => 1],
            ['min_balance' => 0, 'max_balance' => 10, 'cleanup_period' => 7, 'min_lot_balance' => 10, 'disconnect_limit_time' => 15, 'cleanup_time' => '02:00']
        );

        $disconnectLimitTime = $cleanup->disconnect_limit_time;

        try {
            $tradingApiClient = new TradingApiClient;
            $accounts = $tradingApiClient->getAccounts();
            $accounts = collect($accounts);

            foreach ($accounts as $account) {
                try {
                    if (filter_var($account->name, FILTER_VALIDATE_EMAIL)) {
                        $user_account = UserAccount::where('account_id', $account->account_id)
                            ->whereNull('deleted_at') // Ensures the record is not soft-deleted
                            ->whereBetween('created_at', [Carbon::now()->subMinutes(60), Carbon::now()]) // Checks the 10-minute range
                            ->first(); // Retrieves the first matching record

                        if (!is_null($user_account)) {
                            $currentState = $account->state;
                            $lastKnownState = $user_account->last_known_state ?? 'UNKNOWN';
                            $disconnectedAt = $user_account->disconnected_at ?? null;

                            $this->info('Account :' . $account->name . ' Status currentState : ' . $currentState . ' lastKnownState : ' . $lastKnownState);

                            if ($currentState == 'CONNECTED' && $lastKnownState != 'CONNECTED') {
                                $this->handleAccountConnected($account, $user_account);
                            } elseif (($currentState == 'DISCONNECTED' || $currentState == 'WRONG_CREDENTIALS') && $lastKnownState == "UNKNOWN") {
                                if ($disconnectedAt === null) {
                                    // Set the current date when first detected as DISCONNECTED
                                    $user_account->disconnected_at = now();
                                    $user_account->save();
                                    $this->info('Account marked as DISCONNECTED at ' . now());
                                } elseif (now()->diffInMinutes($disconnectedAt) >= $disconnectLimitTime) {
                                    $this->handleAccountDisconnected($account, $user_account);
                                } else {
                                    $this->info('Remain the limit time : ' . ($disconnectLimitTime - now()->diffInMinutes($disconnectedAt)));
                                }
                            }

                            // Update tries and save
                            $user_account->tries++;
                            $user_account->save();
                        }
                    }
                } catch (\Throwable $th) {
                    $this->error($account->name . ' Error happened! ' . $th->getMessage());
                }
            }
        } catch (\Throwable $th) {
            $this->error('Error happened!');
        }

        $this->info('"============== End of AccountsSuccessfullyConnectedCMD "==============');
    }

    private function handleAccountConnected($account, $user_account)
    {
        $my_account = $this->prepareAccountDetails($account, $user_account);
        $this->info('Preparing to send connection email...');

        try {
            $notification = new AccountSuccessfullyConnected($account, $my_account['lang']);
            $notification->sendMail($account);

            $user_account->last_known_state = $account->state;
            $user_account->disconnected_at = null; // Reset the disconnected timestamp
            $user_account->is_notified++;
            $user_account->is_connected++;

            Log::channel('command')->info('email:meta-status => Sent the connection mail to: ' . $account->name);
            $this->info("User name: {$account->name}, account_id: {$account->account_id}, state: {$account->state}, lastKnownState: {$user_account->last_known_state}.");
        } catch (\Throwable $th) {
            $this->error('Failed to send connection email: ' . $th->getMessage());
        }
    }

    private function handleAccountDisconnected($account, $user_account, $shouldDelete = false)
    {
        $my_account = $this->prepareAccountDetails($account, $user_account);
        $this->info('Preparing to send disconnection email...');

        try {
            $notification = new YourAccountIsDisConnected($my_account, $my_account['lang']);
            $notification->sendMail($account->name);

            $user_account->last_known_state = $account->state;
            $user_account->disconnected_at = now(); // Update the timestamp when disconnected
            $user_account->disconnected_at = null;
            $user_account->is_notified++;

            $this->deleteAccount($user_account->account_id);

            Log::channel('command')->info('email:meta-status => Sent the disconnection mail to: ' . $account->name);
            $this->info("User name: {$account->name}, account_id: {$account->account_id}, state: {$account->state}, lastKnownState: {$user_account->last_known_state}.");
        } catch (\Throwable $th) {
            $this->error('Failed to send disconnection email: ' . $th->getMessage());
        }
    }

    private function prepareAccountDetails($account, $user_account)
    {
        return [
            'name' => $account->name,
            'account_id' => $account->login,
            'password' => $account->password,
            'server' => $account->server,
            'lang' => ($user_account->user != null) ? ($user_account->user->lang ?? 'en') : 'en'
        ];
    }

    protected function deleteAccount($accountId)
    {
        $tradingApiClient = new TradingApiClient;
        try {
            // Fetch account details
            $account = $tradingApiClient->getAccount($accountId);

            // Delete the account via the API
            $deleted = $tradingApiClient->deleteAccount($accountId);

            // Remove the account from local storage (database)
            UserAccount::where('account_id', $accountId)->delete();

            $this->info("Successfully deleted account with ID: $accountId");

        } catch (\Exception $e) {
            $this->error("Failed to delete account with ID: $accountId. Error: " . $e->getMessage());
        }
    }
}
