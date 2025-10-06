<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;


use App\Models\User;

class UserController extends Controller {
    //

    function index( Request $request ) {
        $users = [auth()->user()];

        return View( 'client.users.list', compact( 'users' ) );
    }
    public function edit($id)
    {
        $user = User::find($id);
        return view('client.users.edit', compact('user'));
    }

    public function update(Request $request, $id){
        if(isset($request->current_password)){
            $validatedData = $request->validate([
                'current_password' => 'required|string|max:255',
                'lang' => 'required|string|in:pt,en',
                // 'email' => [
                //     'required',
                //     'email',
                //     Rule::unique('users')->ignore($id),
                // ],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            $user = auth()->user();
            $currentPassword = $request->input('current_password');

            if (Hash::check($currentPassword, $user->password)) {
                // Current password matches, proceed with updating the new password
                $newPassword = $request->input('password');
                $user->lang = $request->lang;
                $user->password = Hash::make($newPassword);
                $user->save();

                Session::put('locale', $user->lang);
                App::setLocale($user->lang);
                // Optionally, you can redirect or return a success response
                return redirect()->route(\App\Models\SettingLocal::getLang().'.client.users.index')
                ->with('success', __('User updated successfully.'));
            } else {
                // Current password does not match
                return redirect()->back()->with('error', __('Invalid current password.'));
            }
        }else{
            $validatedData = $request->validate([
                'lang' => 'required|string|in:pt,en',
            ]);
            $user = auth()->user();
            $user->lang = $request->lang;
            $user->save();

            Session::put('locale', $user->lang);
            App::setLocale($user->lang);
            // Optionally, you can redirect or return a success response
            return redirect()->route(\App\Models\SettingLocal::getLang().'.client.users.index')
            ->with('success', __('User updated successfully.'));
        }
    }
}
