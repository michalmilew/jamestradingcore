<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

use App\Models\User;
use App\Models\UserAccount;
use App\Models\TradingApiClient;
use App\Models\TelegramGroup;

use App\Notifications\CustomResetPasswordNotification;
use App\Notifications\UserCreatedNotification;

use App\Models\Cleanup; 
use App\Models\Referral;

class UserController extends Controller
{
    //

    function index(Request $request)
    {
        $users = User::query()
            ->withCount('userAccounts')
            ->with('forcedTelegramGroups');

        if (isset($request->search)) {
            $users = $users->where('email', 'like', "%{$request->search}%")
                ->orWhere('name', 'like', "%{$request->search}%")
                ->orWhere('id_broker', 'like', "%{$request->search}%")
                ->orWhere('ftd', 'like', "%{$request->search}%")
                ->orWhere('notes', 'like', "%{$request->search}%");
        }
        if (isset($request->paid)) {
            $users = $users->where('paid', $request->paid);
        }
        if (isset($request->is_vip)) {
            $users = $users->where('is_vip', $request->is_vip);
        }
        if (isset($request->lang)) {
            $users = $users->where('lang', $request->lang);
        }
        if (isset($request->broker)) {
            $users = $users->where('broker', $request->broker);
        }
        if (isset($request->minlots)) {
            $users = $users->where('lots', '>=', $request->minlots);
        }
        if (isset($request->maxlots)) {
            $users = $users->where('lots', '<=', $request->maxlots);
        }
        if (isset($request->account)) {
            if ($request->account == "Yes") {
                $users = $users->whereHas('userAccounts');
            } else {
                $users = $users->whereDoesntHave('userAccounts');
            }
        }
        if (isset($request->idbroker)) {
            $users = $users->where('id_broker', $request->idbroker);
        }
        
        if (isset($request->sort_by)) {

            $sort_by = $request->sort_by;
            $sortField = explode('-', $sort_by)[0];
            $sortOrder = explode('-', $sort_by)[1];

            if ($sortOrder == "asc") {
                // Sort collection by sortField in ascending order
                $users = $users->orderBy($sortField);
            }
            if ($sortOrder == "desc") {
                // Sort collection by sortField in descending order
                $users = $users->orderByDesc($sortField);
            }
        }

        $users = $users->paginate(10);

        //if no resultsm look for users where has account with login like search input
        if ($users->count() == 0 && isset($request->search)) {
            $accounts = collect();
            try {
                $tradingApiClient = new TradingApiClient;
                $accounts = $tradingApiClient->getAccounts();
                $accounts = collect($accounts);
                $search = $request->search;
                $accounts = $accounts->filter(function ($item) use ($search) {
                    $search = strtolower($search);
                    return strtolower($item['login']) == $search;
                });

                $accounts_ids = $accounts->pluck('account_id')->toArray(); // List of account IDs
                $users = User::whereHas('userAccounts', function ($query) use ($accounts_ids) {
                    $query->whereIn('account_id', $accounts_ids);
                })->paginate(10);
            } catch (\Throwable $th) {
                $accounts = collect();
            }
        } else {
            // //Get UserAccounts to check unused joins
            $users_acounts_ids = UserAccount::whereIn('user_id', $users->pluck('id')->toArray())->pluck('account_id')->toArray();
            try {
                //dd($users_acounts_ids);
                $tradingApiClient = new TradingApiClient;
                $accounts = $tradingApiClient->getAccounts($users_acounts_ids);

                $accounts = collect($accounts);
            } catch (\Throwable $th) {
                $accounts = collect();
            }
        }

        // Get all telegram groups for the checkboxes
        $telegramGroups = TelegramGroup::all();

        $cleanup = Cleanup::firstOrCreate(
            ['id' => 1], // Assuming only one record should exist
            ['min_balance' => 0.01, 'max_balance' => 10, 'min_lot_balance' => 10, 'disconnect_limit_time' => 15, 'cleanup_period' => 7, 'inactive_period' => 15]
        );

        return View('users.list', compact('users', 'accounts', 'cleanup', 'telegramGroups'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        try {
            // First check if email exists
            if (User::where('email', $request->email)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => __('This email is already registered in our system.')
                ], 422);
            }

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'lang' => 'required|string',
                'notes' => 'nullable|string',
                'ig_user' => 'nullable|string',
                'id_broker' => 'nullable|string',
                'is_vip' => 'nullable|integer',
                'ftd' => 'nullable|string',
                'lots' => 'nullable|string',
                'paid' => 'nullable|in:Yes,No',
                'broker' => 'nullable|in:IronFX,T4Trade,Other',
                'restricted_user' => 'required|integer'
            ]);

            $user = User::create($validatedData);

            try {
                Log::channel('web')->info('UserController new user : ' . $user->email);

                $token = Password::createToken($user);
                $url = url(route($user->lang . '.password.set', [
                    'token' => $token,
                    'email' => $user->email,
                ], false));

                Log::channel('web')->info('UserController new url : ' . $url);
                //Notify user
                $notification = new UserCreatedNotification($url, $user->lang);
                $notification->sendMail($user);
            } catch (\Throwable $th) {
                Log::channel('web')->info('UserController : ' . $th);
            }

            return response()->json([
                'success' => true,
                'message' => __('User created successfully.'),
                'redirect' => route(\App\Models\SettingLocal::getLang() . '.admin.users.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function edit($id)
    {
        $user = User::find($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        //dd($request->all());
        $user = User::findOrFail($id);
        if (isset($request->password)) {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'lang' => 'required|string',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->ignore($id),
                ],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'notes' => 'nullable|string',
                'ig_user' => 'nullable|string',
                'id_broker' => 'nullable|string',
                'is_vip' => 'nullable|string',
                'ftd' => 'nullable|string',
                'lots' => 'nullable|string',
                'paid' => 'nullable|in:Yes,No',
                'broker' => 'nullable|in:IronFX,T4Trade,Other',
                'restricted_user' => 'boolean'
            ]);
            $validatedData['password'] = Hash::make($request->password);
        } else {
            $validatedData = $request->validate([
                'name' => 'nullable|string|max:255',
                'lang' => 'required|string',
                'email' => [
                    'nullable',
                    'email',
                    Rule::unique('users')->ignore($id),
                ],
                'notes' => 'nullable|string',
                'ig_user' => 'nullable|string',
                'id_broker' => 'nullable|string',
                'is_vip' => 'nullable|string',
                'ftd' => 'nullable|string',
                'lots' => 'nullable|string',
                'paid' => 'nullable|in:Yes,No',
                'broker' => 'nullable|in:IronFX,T4Trade,Other',
                'restricted_user' => 'boolean'
            ]);
        }
        //$validatedData['is_vip'] = (isset($request->is_vip) && ($request->is_vip == 'on' || $request->is_vip == '1'));
        $user->update($validatedData);
        
        // Handle forced group access
        if ($request->has('forced_groups')) {
            $user->forcedTelegramGroups()->sync($request->forced_groups);
        }

        $user->restricted_user = $request->has('restricted_user') ? 1 : 0;

        if ($user->restricted_user) {
            $user->update(['inactive' => null]);
        }

        $user->save();

        return redirect()->back()
            ->with('success', __('User updated successfully.'));
    }

    public function destroy($id)
    {
        Referral::where('referrer_id', $id)->delete();

        $user = User::find($id);
        $user->delete();

        return redirect()->back()
            ->with('success', __('User deleted successfully.'));
    }

    public function sendResetPasswordLink($id)
    {
        $user = User::find($id);

        // Generate a password reset token
        $token = Password::createToken($user);

        // Send the custom reset password notification
        $notification = new CustomResetPasswordNotification($token, $user->lang);
        $notification->sendMail($user);

        return back()->with('success', __('Password reset link sent!'));
    }

    // Add this new method to handle forced access updates
    public function updateForcedAccess(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);
            $groupIds = $request->input('forced_groups', []);
            
            // Sync the forced access groups
            $user->forcedTelegramGroups()->sync($groupIds);
            
            return response()->json([
                'success' => true,
                'message' => __('Forced access updated successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to update forced access')
            ], 500);
        }
    }
}