<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserAccount;
use App\Models\AccountActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MetaTraderController extends Controller
{
    public function index(Request $request)
    {
        $query = UserAccount::with(['user', 'accountActivity' => function($query) {
            $query->latest()->limit(5);
        }]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('login', 'like', "%{$search}%")
                  ->orWhere('account_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_connected', $request->status === 'connected' ? 1 : 0);
        }

        // Filter by platform
        if ($request->filled('platform')) {
            $query->where('platform_type', $request->platform);
        }

        // Filter by server
        if ($request->filled('server')) {
            $query->where('server', $request->server);
        }

        $accounts = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get unique servers for filter dropdown
        $servers = UserAccount::distinct()->pluck('server')->filter()->values();
        
        // Get unique platforms for filter dropdown
        $platforms = UserAccount::distinct()->pluck('platform_type')->filter()->values();

        // Get statistics
        $stats = [
            'total_accounts' => UserAccount::count(),
            'connected_accounts' => UserAccount::where('is_connected', 1)->count(),
            'disconnected_accounts' => UserAccount::where('is_connected', 0)->count(),
            'mt4_accounts' => UserAccount::where('platform_type', 'MT4')->count(),
            'mt5_accounts' => UserAccount::where('platform_type', 'MT5')->count(),
        ];

        return view('admin.metatrader.index', compact('accounts', 'servers', 'platforms', 'stats'));
    }

    public function show($id)
    {
        $account = UserAccount::with(['user', 'accountActivity' => function($query) {
            $query->latest()->limit(50);
        }])->findOrFail($id);

        return view('admin.metatrader.show', compact('account'));
    }

    public function disconnect($id)
    {
        $account = UserAccount::findOrFail($id);
        $account->update([
            'is_connected' => 0,
            'disconnected_at' => now()
        ]);

        // Record activity
        AccountActivity::create([
            'user_id' => $account->user_id,
            'user_account_id' => $account->id,
            'activity_type' => 'disconnected',
            'details' => [
                'action' => 'Admin disconnect',
                'admin_id' => auth()->id(),
                'timestamp' => now()
            ]
        ]);

        return redirect()->back()->with('success', 'Account disconnected successfully.');
    }

    public function connect($id)
    {
        $account = UserAccount::findOrFail($id);
        $account->update([
            'is_connected' => 1,
            'disconnected_at' => null
        ]);

        // Record activity
        AccountActivity::create([
            'user_id' => $account->user_id,
            'user_account_id' => $account->id,
            'activity_type' => 'connected',
            'details' => [
                'action' => 'Admin connect',
                'admin_id' => auth()->id(),
                'timestamp' => now()
            ]
        ]);

        return redirect()->back()->with('success', 'Account connected successfully.');
    }

    public function assignTemplate(Request $request, $id)
    {
        // Template functionality will be implemented when Template model is created
        return redirect()->back()->with('info', 'Template assignment will be available soon.');
        
        // $request->validate([
        //     'template_id' => 'required|exists:templates,id'
        // ]);

        // $account = UserAccount::findOrFail($id);
        // $account->update(['template_id' => $request->template_id]);

        // // Record activity
        // AccountActivity::create([
        //     'user_id' => $account->user_id,
        //     'user_account_id' => $account->id,
        //     'activity_type' => 'template_assigned',
        //     'details' => [
        //         'action' => 'Template assigned',
        //         'template_id' => $request->template_id,
        //         'admin_id' => auth()->id(),
        //         'timestamp' => now()
        //     ]
        // ]);

        // return redirect()->back()->with('success', 'Template assigned successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:disconnect,connect,delete',
            'account_ids' => 'required|array',
            'account_ids.*' => 'exists:user_accounts,id'
        ]);

        $accounts = UserAccount::whereIn('id', $request->account_ids);

        switch ($request->action) {
            case 'disconnect':
                $accounts->update(['is_connected' => 0, 'disconnected_at' => now()]);
                $message = 'Accounts disconnected successfully.';
                break;
            case 'connect':
                $accounts->update(['is_connected' => 1, 'disconnected_at' => null]);
                $message = 'Accounts connected successfully.';
                break;
            case 'delete':
                $accounts->delete();
                $message = 'Accounts deleted successfully.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    public function logs($id)
    {
        $account = UserAccount::findOrFail($id);
        $logs = AccountActivity::where('user_account_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.metatrader.logs', compact('account', 'logs'));
    }
}
