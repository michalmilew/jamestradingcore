<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TradingAccount;
use App\Models\TradingApiClient;
use App\Models\TradingApiReport;
use App\Models\UserAccount;
use App\Models\Server;
use App\Jobs\AccountWasDeletedJob;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AccountWasDeleted;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class T4TradeController extends Controller
{
    public function index(Request $request){
        try {
            $reportsascollection = collect([]);
            try {
                $url = 'https://go.ironfx.com/api/?command=traders&api_username=bannedpt&api_password=g0326159487&fromdate='.$request["fromdate"].'&todate='.$request["todate"];
                $headers =  array(
                    'Content-Type: application/x-www-form-urlencoded'
                );
                // Open connection
                $ch = curl_init();

                // Setting the options
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                // Execute request
                $response = curl_exec($ch);
                if($response === false) {
                    // Log the error message and error code
                    $error_message = curl_error($ch);
                    $error_code = curl_errno($ch);
                    throw new \Exception("cURL error ({$error_code}): {$error_message}");
                }

                // Convert XML to JSON and then decode it to an associative array
                $jsonString = json_encode(simplexml_load_string(utf8_encode($response)));
                $dataArray = json_decode($jsonString, true);
                // dd($dataArray);
                // Create a Laravel collection
                if($dataArray != []){
                    if(array_key_exists('User_ID', $dataArray["row"])){
                        //dd($dataArray);
                        $reportsascollection = collect([$dataArray["row"]]);
                    }

                    else
                        $reportsascollection = collect($dataArray["row"]);
                }
                if ( isset( $request->search ) ) {
                    $search = $request->search;
                    $reportsascollection = $reportsascollection->filter(function ($item) use ($search) {
                        $search = strtolower($search);
                        return   (str_contains(strtolower($item['User_ID']), $search) || str_contains(strtolower($item['Customer_Name']), $search) || str_contains(strtolower($item['Country']), $search) || str_contains(strtolower($item['generic1']), $search)) ;
                    });
                }


                // Close connection
                curl_close($ch);

            } catch (\Throwable $th) {
                dd($th);
            }

            $perPage = 50; // Number of items per page

            // Get the current page number from the query string, or set it to 1 if not provided
            $page = request()->has('page') ? request('page') : 1;

            // Slice the original collection based on the current page number and the number of items per page
            $slicedData = $reportsascollection->slice(($page - 1) * $perPage, $perPage);

            // Create a LengthAwarePaginator instance to handle pagination
            $reports = new LengthAwarePaginator(
                $slicedData, // The sliced data for the current page
                $reportsascollection->count(), // Total number of items in the original collection
                $perPage, // Number of items per page
                $page, // Current page number
                [
                    'path' => request()->url(), // URL to be used in pagination links
                    'query' => request()->query(), // Query parameters to be included in pagination links
                ]
            );

            return View('t4trade.list', compact('reports'));
        } catch (\Throwable $th) {
            return $th;
            $reports = new LengthAwarePaginator(
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
            return View('t4trade.list', compact('reports' ,'error'));
        }

    }
}
