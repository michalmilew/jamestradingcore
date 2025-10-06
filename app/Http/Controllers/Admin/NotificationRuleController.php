<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationRule;
use Illuminate\Http\Request;

class NotificationRuleController extends Controller
{
    public function index()
    {
        $rules = NotificationRule::all();
        return view('admin.notification-rules.index', compact('rules'));
    }

    public function create()
    {
        return view('admin.notification-rules.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:profit,invite,risk,margin',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'risk_level' => 'required|string|in:All,Low-High,Low,Medium,High,Pro,Pro+,Pro++,Pro+++',
            'notification_class' => 'required|string|max:255',
            'interval' => 'required|string|in:Daily,Weekly,Monthly,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        ]);

        $data = $request->all();
        $data['interval'] = $request->interval ?? 'Monday'; // Set default value to 'Monday'

        NotificationRule::create($data);

        return redirect()->route(\App\Models\SettingLocal::getLang() . '.admin.notification-rules.index')
                         ->with('success', __('Notification Rule created successfully.'));
    }

    public function edit(NotificationRule $notificationRule)
    {
        return view('admin.notification-rules.edit', compact('notificationRule'));
    }

    public function update(Request $request, NotificationRule $notificationRule)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:profit,invite,risk,margin',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'risk_level' => 'required|string|in:All,Low-High,Low,Medium,High,Pro,Pro+,Pro++,Pro+++',
            'notification_class' => 'required|string|max:255',
            'interval' => 'required|string|in:Daily,Weekly,Monthly,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        ]);

        $data = $request->all();
        $data['interval'] = $request->interval ?? 'Monday'; // Set default value to 'Monday'

        $notificationRule->update($data);

        return redirect()->route(\App\Models\SettingLocal::getLang() . '.admin.notification-rules.index')
                         ->with('success', __('Notification Rule updated successfully.'));
    }

    public function destroy(NotificationRule $notificationRule)
    {
        $notificationRule->delete();
        return redirect()->back()->with('success', "Rule deleted successfully.");
    }
}
