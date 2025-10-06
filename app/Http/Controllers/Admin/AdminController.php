<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $admins = Admin::query();

        if ( isset( $request->search ) ) {
            $admins = $admins->where( 'email', 'like', "%{$request->search}%" )
            ->orWhere( 'name', 'like', "%{$request->search}%" );
        }

        $admins = $admins->paginate( 10 );
        return view('admins.list', compact('admins'));
    }

    public function create()
    {
        return view('admins.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $validatedData['password'] = Hash::make($request->password);
        $admin = Admin::create($validatedData);

        return redirect()->route(\App\Models\SettingLocal::getLang().'.admin.admins.index')
            ->with('success', __('Admin created successfully.'));
    }

    public function edit($id)
    {
        $admin = Admin::find($id);
        return view('admins.edit', compact('admin'));
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);        
        if(isset($request->password)){
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('admins')->ignore($id),
                ],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            $validatedData['password'] = Hash::make($request->password);
        }else{
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('admins')->ignore($id),
                ],
            ]);
        }
        $admin->update($validatedData);

        return redirect()->route(\App\Models\SettingLocal::getLang().'.admin.admins.index')
            ->with('success', __('Admin updated successfully.'));
    }

    public function destroy( $id)
    {
        Admin::where('id',$id)->delete();

        return redirect()->route(\App\Models\SettingLocal::getLang().'.admin.admins.index')
            ->with('success', __('Admin deleted successfully.'));
    }
}