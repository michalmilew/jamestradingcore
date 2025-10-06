<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Server;

class ServerController extends Controller
{
    //
    public function index(){
        $servers = Server::paginate(20);

        return View('servers.list', compact('servers'));
    }

    public function create(){
        return View('servers.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255'
        ]);
        $server = Server::create($validatedData);

        return redirect()->route(\App\Models\SettingLocal::getLang().'.admin.servers.index')
            ->with('success', __('Server created successfully.'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255'
        ]);
        $server = Server::find($id);
        $server->update($validatedData);

        return redirect()->route(\App\Models\SettingLocal::getLang().'.admin.servers.index')
            ->with('success', __('Server updated successfully.'));
    }

    public function edit($id){
        $server = Server::find($id);
        return View('servers.edit', compact('server'));
    }


    public function destroy( $id)
    {
        $user = Server::find($id);
        $user->delete();

        return redirect()->route(\App\Models\SettingLocal::getLang().'.admin.servers.index')
            ->with('success', __('Server deleted successfully.'));
    }
}
