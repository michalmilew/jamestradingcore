<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\Api\AccountController;
use App\Http\Controllers\Client\Api\CourseController;
use App\Http\Controllers\Client\Api\DashboardController;
use App\Http\Controllers\Client\Api\LeaderboardController;
use App\Http\Controllers\Client\Api\ReferralController;
use App\Http\Controllers\Client\Api\ReportController;
use App\Http\Controllers\Client\Api\SimulationController;
use App\Http\Controllers\Client\Api\TelegramGroupController;
use App\Http\Controllers\Client\Api\UserController;
use App\Http\Controllers\Client\Api\AuthController;
use App\Http\Controllers\Client\Api\TranslationController;
use App\Http\Controllers\Client\Api\AccountActivityController;
use App\Http\Controllers\Client\Api\NewAccountController;
use App\Http\Controllers\Client\Api\ControllerApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::get('/translations/{locale}', [TranslationController::class, 'index']);
Route::get('/accounts/settings', [AccountController::class, 'getAccountSettings']);
Route::get('/new-accounts/settings', [AccountController::class, 'getAccountSettings']);

Route::get('/new/accounts/api-status', [NewAccountController::class, 'getApiStatus']);
Route::get('/new/accounts/test', [NewAccountController::class, 'test']);
Route::get('/new-accounts/detail/{account_id}', [NewAccountController::class, 'getAccountDetail']);
Route::get('/new-accounts/detail/status/{account_id}', [NewAccountController::class, 'getAccountProgress']);
Route::post('/new-accounts/ea-data', [NewAccountController::class, 'getEaData']);
Route::get('/new-accounts/test-simple', [NewAccountController::class, 'testSimpleEndpoint']);
Route::post('/accounts/test-post', [AccountController::class, 'testPost']);
Route::post('/accounts/send-account-deleted-notification', [AccountController::class, 'sendAccountDeletedNotification']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // User routes
    Route::get('/user', [UserController::class, 'index']);
    Route::put('/user', [UserController::class, 'update']);
    Route::post('/user/password', [UserController::class, 'updatePassword']);

    // Account routes
    Route::get('/accounts/get/{user_id}', [AccountController::class, 'index']);
    Route::post('/accounts', [AccountController::class, 'store']);
    Route::get('/accounts/trading-settings', [AccountController::class, 'getTradingSettings']);
    Route::post('/accounts/trading-settings', [AccountController::class, 'updateTradingSettings']);
    Route::post('/accounts/trading-settings/reset', [AccountController::class, 'resetTradingSettings']);
    Route::get('/accounts/{account_id}', [AccountController::class, 'show']);
    Route::put('/accounts', [AccountController::class, 'update']);
    Route::post('/accounts/update', [AccountController::class, 'update']);
    Route::delete('/accounts', [AccountController::class, 'destroy']);
    Route::post('/accounts/delete', [AccountController::class, 'destroy']);
    Route::post('/accounts/refresh', [AccountController::class, 'refresh']);
    Route::post('/accounts/pause', [AccountController::class, 'pause']);
    Route::post('/accounts/resume', [AccountController::class, 'resume']);
    Route::post('/accounts/update-activity-balance', [AccountController::class, 'updateActivityBalance']);

    Route::get('/new-accounts/get/{user_id}', [NewAccountController::class, 'index']);
    Route::post('/new-accounts', [NewAccountController::class, 'store']);
    Route::get('/new-accounts/trading-settings', [NewAccountController::class, 'getTradingSettings']);
    Route::post('/new-accounts/trading-settings', [NewAccountController::class, 'updateTradingSettings']);
    Route::post('/new-accounts/trading-settings/reset', [NewAccountController::class, 'resetTradingSettings']);
    Route::get('/new-accounts/{account_id}', [NewAccountController::class, 'show']);
    Route::put('/new-accounts', [NewAccountController::class, 'update']);
    Route::post('/new-accounts/update', [NewAccountController::class, 'update']);
    Route::delete('/new-accounts', [NewAccountController::class, 'destroy']);
    Route::post('/new-accounts/delete', [NewAccountController::class, 'destroy']);
    Route::post('/new-accounts/refresh', [NewAccountController::class, 'refresh']);
    Route::post('/new-accounts/pause', [NewAccountController::class, 'pause']);
    Route::post('/new-accounts/resume', [NewAccountController::class, 'resume']);
    Route::post('/new-accounts/update-activity-balance', [NewAccountController::class, 'updateActivityBalance']);

    // Account Activity routes
    Route::get('/account-activities', [AccountActivityController::class, 'index']);

    // Controller API routes (for MT4/MT5 controller)
    Route::get('/controller/all-accounts', [ControllerApiController::class, 'getAllAccounts']);
    Route::get('/controller/server-usage', [ControllerApiController::class, 'getServerUsage']);

    // Course routes
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::get('/courses/{id}/pdf', [CourseController::class, 'showPdf']);

    // Report routes
    Route::get('/reports/{user_id}', [ReportController::class, 'index']);

    // Leaderboard routes
    Route::get('/leaderboard', [LeaderboardController::class, 'index']);
    Route::get('/leaderboard/public', [LeaderboardController::class, 'show']);

    // Simulation routes
    Route::get('/simulation', [SimulationController::class, 'index']);

    // Referral routes
    Route::get('/referrals', [ReferralController::class, 'index']);
    Route::post('/referrals', [ReferralController::class, 'store']);

    // Telegram Group routes
    Route::get('/telegram-groups', [TelegramGroupController::class, 'index']);
    Route::post('/telegram-groups/invite-link', [TelegramGroupController::class, 'getInviteLink']);
    Route::delete('/telegram-groups/{group}', [TelegramGroupController::class, 'leave']);
    Route::post('/telegram-groups/video-language', [TelegramGroupController::class, 'updateVideoLanguage']);
});
