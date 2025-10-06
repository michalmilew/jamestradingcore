<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class PnlController extends Controller
{
    public function index(Request $request)
    {
        try {
            $start = microtime(true); // Start timing

            Log::channel('web')->info("PnlController : Initialized Operation");

            // Load reports from cache
            $data = Storage::disk('local')->get('leaderboard-data.json');
            $users_data = json_decode($data, true);

            // Filter users where the 'name' contains '@'
            $users_data = array_filter($users_data, function($report) {
                return strpos($report['name'], '@') !== false;
            });

            $endReports = microtime(true);
            Log::channel('web')->info('PnlController : Time to load reports from cache: ' . ($endReports - $start) . ' seconds');
            Log::channel('web')->info("PnlController : Users fetched: " . count($users_data));

            // Search functionality
            $loginFilter = $request->input('login', '');
            if ($loginFilter) {
                $users_data = array_filter($users_data, function ($report) use ($loginFilter) {
                    return strpos($report['login'], $loginFilter) !== false;
                });
            }

            // Sorting functionality
            $sort_by = $request->input('sort_by', 'pnl_desc');
            $sortField = explode('_', $sort_by)[0];
            $sortOrder = explode('_', $sort_by)[1];

            $reportsCollection = collect($users_data);

            if ($sortOrder === 'asc') {
                $reportsCollection = $reportsCollection->sortBy($sortField);
            } elseif ($sortOrder === 'desc') {
                $reportsCollection = $reportsCollection->sortByDesc($sortField);
            }

            $perPage = 50; // Number of items per page
            $page = $request->input('page', 1);

            // Slice the collection for pagination
            $slicedData = $reportsCollection->slice(($page - 1) * $perPage, $perPage);

            // Create paginator instance
            $reports = new LengthAwarePaginator(
                $slicedData,
                $reportsCollection->count(),
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );

            return view('pnl.list', compact('reports'));
        } catch (\Throwable $th) {
            Log::channel('web')->error('PnlController : Error in PnlController: ' . $th->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
