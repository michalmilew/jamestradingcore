<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\TradingApiClient;
use App\Models\UserAccount;
use App\Models\Server;
use App\Jobs\AccountWasDeletedJob;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tradingApiClient = new TradingApiClient;
            $accounts = $tradingApiClient->getAccounts();

            $accounts = collect($accounts);

            if (isset($request->search)) {
                $search = $request->search;
                $accounts = $accounts->filter(function ($item) use ($search) {
                    $search = strtolower($search);
                    return $item['type'] == "1" && (str_contains(strtolower($item['login']), $search) || str_contains(strtolower($item['name']), $search) || str_contains(strtolower($item['account']), $search) || str_contains($item['account_id'], $search));
                });
            } else {
                $accounts = $accounts->filter(function ($item) {
                    return $item['type'] == "1";
                });
            }
            //filteer by state
            if (isset($request->state)) {
                $state = $request->state;
                $accounts = $accounts->filter(function ($item) use ($state) {
                    return $item['state'] == $state;
                });
            }
            //filteer by groupid
            if (isset($request->groupid)) {
                $groupid = $request->groupid;
                $accounts = $accounts->filter(function ($item) use ($groupid) {
                    return $item['groupid'] == $groupid;
                });
            }
            //filteer by min
            if (isset($request->min)) {
                $min = $request->min;
                $accounts = $accounts->filter(function ($item) use ($min) {
                    return $item['balance'] >= $min;
                });
            }
            //filteer by max
            if (isset($request->max)) {
                $max = $request->max;
                $accounts = $accounts->filter(function ($item) use ($max) {
                    return $item['balance'] <= $max;
                });
            }
            //filteer by minlots
            if (isset($request->minlots)) {
                $min = $request->minlots;
                $accounts = $accounts->filter(function ($item) use ($min) {
                    $minlots = 0;
                    try {
                        $minlots = $item->userAccounts[0]->lots;
                    } catch (\Throwable $th) {
                        $minlots = 0;
                    }
                    return $minlots >= $min;
                });
            }
            //filteer by maxlots
            if (isset($request->maxlots)) {
                $max = $request->maxlots;
                $accounts = $accounts->filter(function ($item) use ($max) {
                    $maxlots = 0;
                    try {
                        $maxlots = $item->userAccounts[0]->lots;
                    } catch (\Throwable $th) {
                        $maxlots = 0;
                    }
                    return $maxlots <= $max;
                });
            }

            if (isset($request->sort_by)) {

                $sort_by = $request->sort_by;
                $sortField = explode('_', $sort_by)[0];
                $sortOrder = explode('_', $sort_by)[1];
                //dd();
                if ($sortOrder == "asc") {
                    // Sort collection by balance in ascending order
                    $accounts = $accounts->sortBy(function ($item) use ($sortField) {
                        return $item[$sortField];
                    });
                }
                if ($sortOrder == "desc") {
                    // Sort collection by balance in descending order
                    $accounts = $accounts->sortByDesc(function ($item) use ($sortField) {
                        return $item[$sortField];
                    });
                }
            }
            $perPage = 50; // Number of items per page

            // Get the current page number from the query string, or set it to 1 if not provided
            $page = request()->has('page') ? request('page') : 1;

            // Slice the original collection based on the current page number and the number of items per page
            $slicedData = $accounts->slice(($page - 1) * $perPage, $perPage);

            // Create a LengthAwarePaginator instance to handle pagination
            $accounts = new LengthAwarePaginator(
                $slicedData, // The sliced data for the current page
                $accounts->count(), // Total number of items in the original collection
                $perPage, // Number of items per page
                $page, // Current page number
                [
                    'path' => request()->url(), // URL to be used in pagination links
                    'query' => request()->query(), // Query parameters to be included in pagination links
                ]
            );
            //$users = $users->paginate( 10 );
            return View('accounts.list', compact('accounts'));
        } catch (\Throwable $th) {
            $accounts = new LengthAwarePaginator(
                collect([]), // The sliced data for the current page
                0, // Total number of items in the original collection
                50, // Number of items per page
                1, // Current page number
                [
                    'path' => request()->url(), // URL to be used in pagination links
                    'query' => request()->query(), // Query parameters to be included in pagination links
                ]
            );
            $error = $th->getMessage();
            return View('accounts.list', compact('accounts', 'error'));
        }

    }
    public function refreshindex(Request $request)
    {
        // //Get UserAccounts to check unused joins
        $account_ids = $request->account_ids;
        try {
            //dd($users_acounts_ids);
            $tradingApiClient = new TradingApiClient;
            $accounts = $tradingApiClient->getAccounts($account_ids);

            $accounts = collect($accounts);
            foreach ($accounts as $account) {
                $account->state = __($account->state);
            }

            return response()->json(['data' => $accounts], 200, []);
        } catch (\Throwable $th) {
            $accounts = collect();
            return response()->json(['data' => $th->getMessage()], 200, []);
        }
    }
    public function create(Request $request)
    {
        $servers = Server::all();
        return View('accounts.create', compact('servers'));
    }
    public function store(Request $request)
    {
        Log::channel('web')->info('========= AccountController ==========');
        // Validate the form input
        $validatedData = $request->validate([
            //'type' => 'required|integer|in:0,1',
            //'name' => 'required|string|max:255',
            //'broker' => 'required|string|in:mt4,mt5,ctrader,fxcm_fc,lmax',
            'login' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'server' => 'nullable|string',
            'groupid' => 'nullable|string|max:255',
            //'subscription' => 'required|string',
            //'comment' => 'nullable|string|max:255',
        ]);
        //bXciiLZp, tXciiLZp
        //$validatedData['group_id'] = 'aXciiLZp';//bXciiLZp, tXciiLZp
        $validatedData['name'] = $validatedData['login'];
        $validatedData['status'] = '1';
        $validatedData['subscription'] = '8O56WT9IIVRX';
        $validatedData['type'] = '1';
        $validatedData['pending'] = '0';
        $validatedData['stop_loss'] = '0';
        $validatedData['take_profit'] = '0';
        $validatedData['alert_email'] = '0';
        $validatedData['alert_sms'] = '0';
        $validatedData['environment'] = 'Real';
        $validatedData['broker'] = 'mt4';
        $validatedData['password'] = trim($validatedData['password']);
        $validatedData['login'] = trim($validatedData['login']);
        //$validatedData[''] = '';

        // Add this before creating/saving the user
        $request->merge([
            'restricted_user' => $request->has('restricted_user') ? 1 : 0
        ]);

        try {
            $tradingApiClient = new TradingApiClient;
            $accounts = $tradingApiClient->createAccount($validatedData);

            // Ensure the account is set to active status after creation
            $updateData = [
                'account_id' => $accounts->account_id,
                'status' => '1', // Set status to 1 for active
                'name' => $validatedData['login']
            ];
            
            // Update the account to ensure it's active
            $accounts = $tradingApiClient->updateAccount($updateData);

            $userAccountData = [
                //No user when adding account by admin
                'tries' => 0,
                'is_connected' => 0,
                'is_notified' => 0,
                'created_at' => Carbon::now(),
                'account_id' => $accounts->account_id,
                'login' => $validatedData['login'],
            ];
            $trashedAccounts = UserAccount::withTrashed()->where('login', $validatedData['login'])->get();

            if ($trashedAccounts->count() > 0) {
                foreach ($trashedAccounts as $ta) {
                    if ($ta->trashed()) {
                        $ta->restore();
                    }
                }
                UserAccount::where('login', $validatedData['login'])->update($userAccountData);
            } else {
                UserAccount::create($userAccountData);
            }
            //$validatedData['account_id'] = $accounts->account_id;
            //$accounts = $tradingApiClient->updateAccount($validatedData);
            return redirect()->route(\App\Models\SettingLocal::getLang() . '.admin.accounts.index')->with('success', __('MetaTrader 4 Account created successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

    }

    public function show($account_id)
    {
        $tradingApiClient = new TradingApiClient;
        $account = $tradingApiClient->getAccount($account_id);

        return View('accounts.show', compact('account'));
    }

    public function details($account_id)
    {
        $tradingApiClient = new TradingApiClient;
        $account = $tradingApiClient->getAccount($account_id);

        return View('accounts.show', compact('account'));
    }

    public function edit($account_id)
    {
        $tradingApiClient = new TradingApiClient;
        $account = $tradingApiClient->getAccount($account_id);
        $servers = Server::all();
        return View('accounts.edit', compact('account', 'servers'));
    }
    public function update(Request $request, $account_id)
    {
        // Validate the form input
        $validatedData = $request->validate([
            //'type' => 'required|integer|in:0,1',
            //'name' => 'required|string|max:255',
            //'broker' => 'required|string|in:mt4,mt5,ctrader,fxcm_fc,lmax',
            'login' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'server' => 'nullable|string',
            //'environment' => 'required|string|in:Real,Demo',
            //'status' => 'required|string|in:0,1',
            'groupid' => 'nullable|string|max:255',
            //'subscription' => 'nullable|string',
            //'pending' => 'required|integer|in:0,1',
            //'stop_loss' => 'required|integer|in:0,1',
            //'take_profit' => 'required|integer|in:0,1',
            //'comment' => 'nullable|string|max:255',
            //'alert_email' => 'required|integer|in:0,1',
            //'alert_sms' => 'required|integer|in:0,1',
        ]);
        $validatedData['account_id'] = $account_id;
        // $validatedData['group_id'] = 'aXciiLZp';//bXciiLZp, tXciiLZp
        //$validatedData['group_id'] = 'aXciiLZp';//bXciiLZp, tXciiLZp
        //$validatedData['name'] = $validatedData['login'];
        $validatedData['status'] = '1';
        //$validatedData['subscription'] = 'auto';
        $validatedData['type'] = '1';
        $validatedData['pending'] = '0';
        $validatedData['stop_loss'] = '0';
        $validatedData['take_profit'] = '0';
        $validatedData['alert_email'] = '0';
        $validatedData['alert_sms'] = '0';
        $validatedData['environment'] = 'Real';
        $validatedData['broker'] = 'mt4';
        $validatedData['password'] = trim($validatedData['password']);
        $validatedData['login'] = trim($validatedData['login']);
        try {
            $tradingApiClient = new TradingApiClient;
            $accounts = $tradingApiClient->updateAccount($validatedData);
            UserAccount::where('account_id', $account_id)->update(['login' => $validatedData['login']]);
            return redirect()->route(\App\Models\SettingLocal::getLang() . '.admin.accounts.index')->with('success', __('MetaTrader 4 Account updated successfully'));
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
                    'name' => $account->login,
                    'account_id' => $account->login,
                    'password' => $account->password,
                    'server' => $account->server,
                ];
                dispatch(new AccountWasDeletedJob($my_account));
            }
            return redirect()->back()
                ->with('success', __('MetaTrader 4 Account deleted successfully'));
        } catch (\Exception $e) {
            return redirect()->route(\App\Models\SettingLocal::getLang() . '.admin.accounts.index')
                ->with('error', $e->getMessage());
        }

    }

}
