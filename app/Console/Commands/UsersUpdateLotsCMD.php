<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserAccount;
use App\Models\User;
use App\Models\TradingApiPosition;
use Illuminate\Support\Facades\Log;

class UsersUpdateLotsCMD extends Command
{
    protected $signature = 'calculate:userslots';
    protected $description = 'Notifier admin des new connected accounts';

    public function handle()
    {
        Log::channel('command')->info('Starting of UsersUpdateLotsCMD!');

        try {
            $usersaccounts = UserAccount::where('is_connected', 1)->orderBy('updated_at')->get();

            foreach ($usersaccounts as $user_account) {
                try {
                    $tradingApiPosition = new TradingApiPosition;
                    $positions = $tradingApiPosition->getClosedPositions($user_account->account_id);

                    $position = max($positions->sum('amountLot'), $user_account->lots);
                    User::where('id', $user_account->user_id)
                        ->where(function ($query) use ($position) {
                            $query->where('lots', '<', $position)
                                  ->orWhereNull('lots');
                        })
                        ->update(['lots' => $position]);

                    $user_account->lots = $position;
                    $user_account->save();

                    $this->info('Processed: ' . $user_account->account_id . ' Lots: ' . $positions->count() . ': ' . $position);
                } catch (\Throwable $th) {
                    $this->info($user_account);
                    $this->info($th->getMessage());
                }
            }

            Log::channel('command')->info('End of UsersUpdateLotsCMD!');
        } catch (\Throwable $th) {
            Log::channel('command')->info($th->getMessage());
        }
    }
}
