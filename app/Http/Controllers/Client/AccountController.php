<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

use App\Models\TradingApiClient;
use App\Models\UserAccount;
use App\Models\Admin;
use App\Models\Server;
use App\Models\RiskSetting;
use App\Jobs\AccountWasDeletedJob;
use App\Notifications\AccountSuccessfullyAdded;
use Carbon\Carbon;


class AccountController extends Controller
{
    public function index(Request $request)
    {
        Log::channel('web')->info('AccountController Index');
        $user = auth()->user();

        if($user->restricted_user === 1) {
            return redirect()->route(\App\Models\SettingLocal::getLang() . '.client.courses.index');
        }

        $userAccounts = $user->userAccounts->pluck('account_id')->toArray();

        if (count($userAccounts) > 0) {
            Log::channel('web')->info('AccountController userAccounts : ' . count($userAccounts));
            try {
                $tradingApiClient = new TradingApiClient;
                $accounts = $tradingApiClient->getAccounts($userAccounts);
                $accounts = collect($accounts);
                $currentTime = Carbon::now();
                foreach ($accounts as $account) {
                    // Assuming you have an $account object with a 'created_at' attribute
                    //$user->userAccounts->where('account_id',$account->account_id)->first()->updated_at;
                    $accountCreatedAt = Carbon::parse($user->userAccounts->where('account_id', $account->account_id)->first()->created_at);
                    // Calculate the time difference in minutes
                    $timeDifferenceInMinutes = $accountCreatedAt->diffInMinutes($currentTime);
                    if ($timeDifferenceInMinutes >= 2 || $account->state == 'CONNECTED') {
                        // Account was created more than two minutes ago
                        $account->state = __($account->state);
                    } else {
                        // Account was created within the last two minutes
                        $account->state = __('NONE');
                    }
                }

                Log::channel('web')->info('AccountController accounts : ' . count($accounts));

                if(count(collect($accounts)) > 0) {
                    $header = __('MetaTrader 4 Account');
                    return View('client.accounts.list', compact('accounts', 'header'));
                } else {
                    foreach($userAccounts as $userAccount) {
                        UserAccount::where('account_id', $userAccount)->delete();
                    }
                    return redirect()->route(\App\Models\SettingLocal::getLang() . '.client.accounts.create');
                }
            } catch (\Exception $e) {
                
                $accounts = [];
                $accounts = collect($accounts);

                $error = $e->getMessage();
                
                foreach($userAccounts as $userAccount) {
                    Log::channel('web')->info('AccountController userAccount : ' . $userAccount);
                    UserAccount::where('account_id', $userAccount)->delete();
                }
                return redirect()->route(\App\Models\SettingLocal::getLang() . '.client.accounts.create');
            }
        } else {
            return redirect()->route(\App\Models\SettingLocal::getLang() . '.client.accounts.create');
        }
    }

    public function refreshindex(Request $request)
    {
        Log::channel('web')->info('AccountController refreshindex');
        // //Get UserAccounts to check unused joins
        $account_ids = $request->account_ids;
        $user = auth()->user();
        try {
            //dd($users_acounts_ids);
            $tradingApiClient = new TradingApiClient;
            $accounts = $tradingApiClient->getAccounts($account_ids);

            $accounts = collect($accounts);

            $currentTime = Carbon::now();
            foreach ($accounts as $account) {
                // Assuming you have an $account object with a 'created_at' attribute
                $accountCreatedAt = Carbon::parse($user->userAccounts->where('account_id', $account->account_id)->first()->created_at);
                // Calculate the time difference in minutes
                $timeDifferenceInMinutes = $accountCreatedAt->diffInMinutes($currentTime);
                if ($timeDifferenceInMinutes >= 2 || $account->state == 'CONNECTED') {
                    // Account was created more than two minutes ago
                    $account->state = __($account->state);
                } else {
                    // Account was created within the last two minutes
                    $account->state = __('NONE');
                }
            }
            return response()->json(['data' => $accounts], 200, []);
        } catch (\Throwable $th) {
            $accounts = collect();
            return response()->json(['data' => $th], 200, []);
        }
    }

    public function create(Request $request)
    {
        Log::channel('web')->info('AccountController create');
        $servers = Server::all();
        $enabledRiskSettings = RiskSetting::where('enabled', 1)->get();

        return View('client.accounts.create', compact('servers', 'enabledRiskSettings'));
    }

    public function store(Request $request)
    {
        Log::channel('web')->info('========= AccountController ==========');

        $start_time = microtime(true);

        $validatedData = $request->validate([
            'type' => 'required|integer|in:1',
            'login' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'server' => 'nullable|string',
            'groupid' => 'nullable|string|max:255',
            'subscription' => 'required|string',
        ]);

        $validatedData['name'] = auth()->user()->email;
        $validatedData['status'] = '1';
        $validatedData['broker'] = 'mt4';
        $validatedData['password'] = trim($validatedData['password']);
        $validatedData['login'] = trim($validatedData['login']);
        $validatedData['environment'] = 'Real';

        $login = trim($validatedData['login']);

        Log::channel('web')->info('login : ' . $login);

        $tradingApiClient = new TradingApiClient;

        try {
            // Check if the account with the same login already exists
            $accountExists = $tradingApiClient->accountExists($login);

            if ($accountExists !== '0') {
                $tradingApiClient->deleteAccount($accountExists);
            }

            $end_time = microtime(true);

            Log::channel('web')->info('Time 1 : ' . number_format($end_time - $start_time, 4) . " seconds");

            $start_time = microtime(true);
            // Create the account
            $account = $tradingApiClient->createAccount($validatedData);

            // Ensure the account is set to active status after creation
            $updateData = [
                'account_id' => $account->account_id,
                'status' => '1', // Set status to 1 for active
                'name' => auth()->user()->email
            ];
            
            // Update the account to ensure it's active
            $account = $tradingApiClient->updateAccount($updateData);

            $end_time = microtime(true);

            Log::channel('web')->info('Time 2 : ' . number_format($end_time - $start_time, 4) . " seconds");

            $start_time = microtime(true);

            // Save account data to UserAccount
            $userAccountData = [
                'user_id' => auth()->user()->id,
                'tries' => 0,
                'is_connected' => 0,
                'is_notified' => 0,
                'created_at' => Carbon::now(),
                'account_id' => $account->account_id,
                'login' => $login,
            ];

            $trashedAccounts = UserAccount::withTrashed()->where('login', $login)->get();

            UserAccount::create($userAccountData);
            
            // Notify admin about the new account
            $adminEmail = \App\Models\SettingLocal::getAdminEmail();
            $my_account = [
                'name' => $account->name,
                'account_id' => $account->login,
                'password' => $account->password,
                'server' => $account->server,
            ];

            $end_time = microtime(true);

            Log::channel('web')->info('Time 3 : ' . number_format($end_time - $start_time, 4) . " seconds");

            $start_time = microtime(true);

            $admin = Admin::where('email', $adminEmail)->firstOrFail();

            $notification = new AccountSuccessfullyAdded($my_account, $admin->lang);
            $notification->sendMail($adminEmail);

            $end_time = microtime(true);

            Log::channel('web')->info('Time 4 : ' . number_format($end_time - $start_time, 4) . " seconds");

            // return redirect()->route(\App\Models\SettingLocal::getLang() . '.client.accounts.index')
            //     ->with('success', __('MetaTrader 4 Account Added successfully'));

            return response()->json([
                'success' => true,
                'message' => __('MetaTrader 4 Account Added successfully'),
            ]);
        } catch (\Exception $e) {
            Log::channel('web')->info($e);
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    

    public function show($account_id)
    {
        $tradingApiClient = new TradingApiClient;
        $account = $tradingApiClient->getAccount($account_id);

        return View('client.accounts.show', compact('account'));
    }

    public function edit($account_id)
    {
        $tradingApiClient = new TradingApiClient;
        $account = $tradingApiClient->getAccount($account_id);
        $servers = Server::all();
        $enabledRiskSettings = RiskSetting::where('enabled', 1)->get();

        return View('client.accounts.edit', compact('account', 'servers', 'enabledRiskSettings'));
    }
    public function update(Request $request, $account_id)
    {
        // Validate the form input
        $validatedData = $request->validate([
            //'type' => 'required|integer|in:0,1',
            //'name' => 'required|string|max:255',
            //'broker' => 'required|string|in:mt4',//,mt5,ctrader,fxcm_fc,lmax',
            //'login' => 'required|string|max:255',
            //'password' => 'required|string|max:255',
            //'server' => 'nullable|string',
            //'environment' => 'required|string|in:Real',//,Demo',
            //'status' => 'required|string|in:1',
            'groupid' => 'nullable|string|max:255',
            //'subscription' => 'nullable|string',
            //'pending' => 'required|integer|in:1',
            //'stop_loss' => 'required|integer|in:1',
            //'take_profit' => 'required|integer|in:1',
            //'comment' => 'nullable|string|max:255',
            //'alert_email' => 'required|integer|in:0',
            //'alert_sms' => 'required|integer|in:0',
        ]);
        $validatedData['account_id'] = $account_id;
        $validatedData['status'] = '1';
        $validatedData['name'] = auth()->user()->email;
        try {
            $tradingApiClient = new TradingApiClient;
            $account = $tradingApiClient->updateAccount($validatedData);
            //dispatch(new AccountSuccessfullyConnectedJob($account_id));
            $adminEmail = \App\Models\SettingLocal::getAdminEmail();
            // $my_account = [
            //     'name' => $account->name,
            //     'account_id' => $account->login,
            //     'password' => $account->password,
            //     'server' => $account->server,
            // ];
            //Notification::route('mail', $adminEmail)
            //        ->notify((new AccountSuccessfullyUpdated($my_account))->onQueue('notifications'));
            UserAccount::where('user_id', auth()->user()->id)
                ->where('account_id', $account_id)->update(['login' => $account->login]);
            // return redirect()->route(\App\Models\SettingLocal::getLang() . '.client.accounts.index')->with('success', __('MetaTrader 4 Account Added successfully'));

            return response()->json([
                'success' => true,
                'message' => __('MetaTrader 4 Account updated successfully!'),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

    }
    public function destroy($account_id)
    {
        $tradingApiClient = new TradingApiClient;
        try {
            $account = $tradingApiClient->getAccount($account_id);
            $account1 = $tradingApiClient->deleteAccount($account_id);

            UserAccount::where('account_id', $account_id)->delete();
            if ($account->state == 'CONNECTED') {
                $my_account = [
                    'name' => auth()->user()->email,
                    'account_id' => $account->login,
                    'password' => $account->password,
                    'server' => $account->server,
                ];
                dispatch(new AccountWasDeletedJob($my_account));
            }

            // return redirect()->route(\App\Models\SettingLocal::getLang() . '.client.accounts.index')
            //     ->with('success', __('MetaTrader 4 Account Deleted successfully'));
            
            return response()->json([
                'success' => true,
                'message' => __('MetaTrader 4 Account Deleted successfully'),
            ]);
        } catch (\Exception $e) {
            return redirect()->route(\App\Models\SettingLocal::getLang() . '.client.accounts.index')
                ->with('error', $e->getMessage());
        }

    }

}
