<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = Storage::disk('local')->get('leaderboard-data.json');
            $users = json_decode($data, true);

            // Validate users to ensure they have the required properties
            $validatedUsers = array_filter($users, function ($user) {
                return isset($user['name'], $user['pnl']) && is_numeric($user['pnl']);
            });

            Log::channel('web')->info("LeaderboardController : Validated Users: " . count($validatedUsers));

            // Convert each validated user to an object
            $validatedUsers = array_map(function ($user) {
                return (object) $user;
            }, $validatedUsers);

            // Filter users with '@' in their name
            $topUsers = collect($validatedUsers)->filter(function ($item) {
                return str_contains($item->name, '@');
            });

            // Sort collection by balance in descending order and take top 25
            $topUsers = $topUsers->sortByDesc(function ($item) {
                return $item->pnl;
            })->take(25);

            return view('client.leaderboard', compact('topUsers'));
        } catch (\Throwable $th) {
            // Log the exception
            Log::channel('web')->error('LeaderboardController : Error in LeaderboardController@show: ' . $th->getMessage());
            return $th;
        }
    }

    public function show(Request $request)
    {
        try {
            $data = Storage::disk('local')->get('leaderboard-data.json');
            $users = json_decode($data, true);

            // Validate users to ensure they have the required properties
            $validatedUsers = array_filter($users, function ($user) {
                return isset($user['name'], $user['pnl']) && is_numeric($user['pnl']);
            });

            Log::channel('web')->info("LeaderboardController : Validated Users: " . count($validatedUsers));

            // Convert each validated user to an object
            $validatedUsers = array_map(function ($user) {
                return (object) $user;
            }, $validatedUsers);

            // Filter users with '@' in their name
            $topUsers = collect($validatedUsers)->filter(function ($item) {
                return str_contains($item->name, '@');
            });

            // Sort collection by balance in descending order and take top 25
            $topUsers = $topUsers->sortByDesc(function ($item) {
                return $item->pnl;
            })->take(25);

            return view('client.public-leaderboard', compact('topUsers'));
        } catch (\Throwable $th) {
            // Log the exception
            Log::channel('web')->error('LeaderboardController : Error in LeaderboardController@show: ' . $th->getMessage());
            return $th;
        }
    }
}
