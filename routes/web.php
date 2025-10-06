<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnsubscribeController;
use App\Http\Controllers\Admin\NotificationTestController;
use App\Http\Controllers\Admin\TestProfitController;
use App\Http\Controllers\Client\TelegramGroupController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Admin\AffiliateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//Route::get('/test-email', function () {
    //try {
        //\Illuminate\Support\Facades\Mail::raw('Test email', function ($message) {
            //$message->to('your_email@example.com')
                    //->subject('Test Email');
        //});
        //return 'Email sent successfully';
    //} catch (\Exception $e) {
        //return 'Error sending email: ' . $e->getMessage();
    //}
//});


Route::get('/test', function(){
    $report  = [];
    try {
        //Log::info($this->description);
        $usersaccounts = \App\Models\UserAccount::all();
        $tradingApiClient = new \App\Models\TradingApiClient;
        $accounts = $tradingApiClient->getAccounts4();
        foreach ($usersaccounts as $user_account) {

            try {

                $account = $tradingApiClient->getAccount( $user_account->account_id );

                $user_account->login = $account->login;
                $report[]= $user_account->account_id.' login has seted with '.$account->login;
                try {


                    foreach ($accounts as $a) {
                        if($a->login == $account->login){
                            $user_account->account_id2 = $a->account_id;
                            $report[]= $user_account->account_id.' has new id';
                        }

                    }
                } catch (\Throwable $th) {
                    $report[]= $user_account->account_id. ' '. $th->getMessage() ;
                }

            } catch (\Throwable $th) {
                $report[]= 'No account for : '.$user_account->account_id  ;
                Log::info($user_account);
                Log::info($th);
            }
            $user_account->save();
        }
    } catch (\Throwable $th) {
        Log::info($th);
        $report[]=  $th->getMessage() ;
    }
    dd( $report);
});
Route::get('/test2', function(){


    try {
        $url = 'https://go.ironfx.com/api/?command=traders&api_username=bannedpt&api_password=g0326159487&fromdate=2021-05-20&todate=2024-05-2';
        $url = 'https://go.t4trade.com/api/?command=traders&api_username=bannedpt&api_password=g0326159487&fromdate=2021-05-20&todate=2024-05-2';
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
        return($jsonString);
        $dataArray = json_decode($jsonString, true);

        // Create a Laravel collection
        $collection = collect($dataArray);


        return $collection;
        // Close connection
        curl_close($ch);

    } catch (\Throwable $th) {
        dd($th);
    }
});
Route::get('/', function () {
    return redirect()->route(\App\Models\SettingLocal::getLang().'.client.accounts.index');
});

// Route::get('/admin', function () {
//     return redirect()->route(\App\Models\SettingLocal::getLang().'.admin.dashboard');
// });
Route::get('/lang/{lang}',[App\Http\Controllers\Controller::class, 'setLanguage'] )->name('settinglang');

// Language-specific routes
foreach (\App\Models\SettingLocal::getLangs() as $key => $value) {
    Route::group([ 'prefix' => $key,'as' => $key.'.client.', 'namespace' => '\App\Http\Controllers\Client', 'middleware' => ['auth']],function(){
        require __DIR__.'/client.php';
        
        // Telegram Group routes
        Route::get('/telegram-groups', [TelegramGroupController::class, 'index'])->name('telegram-groups.index');
        Route::post('/telegram/invite-link', [TelegramGroupController::class, 'getInviteLink'])->name('telegram.invite-link');
        Route::post('/telegram-groups/{group:slug}/join', [App\Http\Controllers\Client\TelegramGroupController::class, 'join'])->name('telegram-groups.join');
        Route::post('/telegram-groups/{group:slug}/leave', [App\Http\Controllers\Client\TelegramGroupController::class, 'leave'])->name('telegram-groups.leave');
        Route::post('/telegram-groups/{group:slug}/check-balance', [App\Http\Controllers\Client\TelegramGroupController::class, 'checkBalance'])->name('telegram-groups.check-balance');
    });

    Route::group([ 'prefix' => $key.'/admin','as' => $key.'.admin.', 'namespace' => '\App\Http\Controllers\Admin', 'middleware' => ['auth:admin']],function(){
        require __DIR__.'/admin.php';
    });

    Route::group([ 'prefix' => $key,'as' => $key.'.', 'namespace' => '\App\Http\Controllers\Client'],function(){
        require __DIR__.'/auth.php';
    });

    Route::group([ 'prefix' => $key,'as' => $key.'.'],function(){
        Route::get('/top', [App\Http\Controllers\Client\LeaderboardController::class, 'show'])->name('leaderboard.show');
        // Routes for the admin guard
        Route::prefix('admin')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('admin.login');
            Route::post('/', [App\Http\Controllers\Admin\Auth\LoginController::class, 'login']);
            Route::post('/logout', [App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->middleware(['auth:admin'])->name('admin.logout');
        });
    });
}

// Static pages
Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');
Route::view('/earnings-disclaimer', 'earnings-disclaimer')->name('earnings-disclaimer');
Route::view('/terms-and-conditions', 'terms-and-conditions')->name('terms-and-conditions');

// Unsubscribe route
Route::get('unsubscribe/{email}', [UnsubscribeController::class, 'unsubscribe'])->name('unsubscribe');

//use App\Jobs\FetchTradingData;

//Route::get('/run-fetch-job', function () {
    //FetchTradingData::dispatch();
    //return 'Job dispatched!';
//});


// routes/web.php

// routes/web.php

// Admin routes
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/admin/testprofit', [TestProfitController::class, 'form'])->name('admin.testprofit.form');
    Route::post('/admin/testprofit/send', [TestProfitController::class, 'send'])->name('admin.testprofit.send');

    Route::post('/admin/notifications/conditional/send', [NotificationTestController::class, 'sendConditionalNotifications'])
    ->name('admin.notification.conditional.send');

    Route::post('/admin/affiliates/default-price', [AffiliateController::class, 'updateDefaultReferralPrice'])
        ->name('admin.affiliates.default-price');
});

// Password reset route
Route::get('reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'create'])->name('password.reset');
