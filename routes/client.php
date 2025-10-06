<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\ReferralController;
use App\Http\Controllers\Client\TelegramGroupController;

//Simulation
Route::get('/simulation', 'SimulationController')->name('simulation');
//Reports
Route::get('/reports', 'ReportController@index')->name('reports.index');
//Manage users
Route::get('/users', 'UserController@index')->name('users.index');
Route::get('/users/user/{id}', 'UserController@edit')->name('users.edit');
Route::post('/users/user/{id}', 'UserController@update')->name('users.update');
//Manage Accounts
Route::get('/accounts', 'AccountController@index')->name('accounts.index');
Route::post('/accounts/refreshindex', 'AccountController@refreshindex')->name('accounts.refreshindex');

//Manage courses
Route::get('/courses', 'CourseController@index')->name('courses.index');
Route::get('/courses/show/{id}', 'CourseController@show')->name('courses.show');
Route::get('/courses/showpdf/{id}', 'CourseController@showpdf')->name('courses.showpdf');
//Route::get('/accounts/show/{account_id}', 'AccountController@show')->name('accounts.show');
Route::get('/accounts/new_account', 'AccountController@create')->name('accounts.create');
Route::post('/accounts/new_account', 'AccountController@store')->name('accounts.store');
Route::get('/accounts/account/{account_id}', 'AccountController@edit')->name('accounts.edit');
Route::post('/accounts/account/{account_id}', 'AccountController@update')->name('accounts.update');
Route::delete('/accounts/account/{account_id}', 'AccountController@destroy')->name('accounts.destroy');


//Unsubscribe from emails
Route::get('/unsubscribe/{user}', [App\Http\Controllers\UnsubscribeController::class, 'unsubscribe'])->name('unsubscribe');

//Top Rank
Route::get('/top-rank', 'LeaderboardController@index')->name('top-rank');

Route::get('/referrals', [ReferralController::class, 'index'])
    ->name('referrals.index');
Route::post('/referrals', [ReferralController::class, 'store'])
    ->name('referrals.store');

Route::get('/telegram-groups', [TelegramGroupController::class, 'index'])
    ->name('telegram-groups.index');
    
Route::post('/telegram-groups/get-invite-link', [TelegramGroupController::class, 'getInviteLink'])
    ->name('telegram-groups.get-invite-link');
    
Route::delete('/telegram-groups/{group}/leave', [TelegramGroupController::class, 'leave'])
    ->name('telegram-groups.leave');

Route::post('/telegram-groups/update-video-language', [TelegramGroupController::class, 'updateVideoLanguage'])
    ->name('telegram-groups.update-video-language');