<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TradingApiGroup;

class GroupController extends Controller {
    //

    public function index() {
        $tradingApiGroup = new TradingApiGroup;

        try {
            $groups = $tradingApiGroup->getGroups();
            return View( 'groups.list', compact( 'groups' ) );
        } catch ( \Exception $e ) {
            $groups = [];
            return View( 'groups.list', compact( 'groups' ) )->with( 'error', $e->getMessage() );
        }

    }
}
