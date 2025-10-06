<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TelegramSettingsController;
use App\Http\Controllers\Admin\AffiliateController;
use App\Http\Controllers\Admin\MetaTraderController;

Route::post('/setting/setparams',[App\Http\Controllers\Controller::class, 'setparams'] )->name('settingsetparams');

Route::get('/dashboard', 'DashboardController')->middleware(['auth:admin'])->name('dashboard');
//Manage admins
Route::get('/admins', 'AdminController@index')->name('admins.index');
Route::get('/admins/show/{id}', 'AdminController@show')->name('admins.show');
Route::get('/admins/new_admin', 'AdminController@create')->name('admins.create');
Route::post('/admins/new_admin', 'AdminController@store')->name('admins.store');
Route::get('/admins/admin/{id}', 'AdminController@edit')->name('admins.edit');
Route::post('/admins/admin/{id}', 'AdminController@update')->name('admins.update');
Route::delete('/admins/admin/{id}', 'AdminController@destroy')->name('admins.destroy');
//Manage servers
Route::get('/servers', 'ServerController@index')->name('servers.index');
Route::get('/servers/create', 'ServerController@create')->name('servers.create');
Route::post('/servers/create', 'ServerController@store')->name('servers.store');
Route::get('/servers/server/{id}', 'ServerController@edit')->name('servers.edit');
Route::post('/servers/server/{id}', 'ServerController@update')->name('servers.update');
Route::delete('/servers/server/{id}', 'ServerController@destroy')->name('servers.destroy');
//Manage users
Route::get('/users', 'UserController@index')->name('users.index');
Route::get('/users/show/{id}', 'UserController@show')->name('users.show');
Route::get('/users/new_user', 'UserController@create')->name('users.create');
Route::post('/users/new_user', 'UserController@store')->name('users.store');
Route::get('/users/user/{id}', 'UserController@edit')->name('users.edit');
Route::post('/users/user/{id}', 'UserController@update')->name('users.update');
Route::delete('/users/user/{id}', 'UserController@destroy')->name('users.destroy');
Route::post('/users/sendresetpasswordlink/{id}', 'UserController@sendResetPasswordLink')->name('users.sendresetpasswordlink');
//Manage Accounts
Route::get('/accounts', 'AccountController@index')->name('accounts.index');
Route::post('/accounts/refreshindex', 'AccountController@refreshindex')->name('accounts.refreshindex');
Route::get('/accounts/account/{account_id}', 'AccountController@show')->name('accounts.show');
Route::get('/accounts/new_account', 'AccountController@create')->name('accounts.create');
Route::post('/accounts/new_account', 'AccountController@store')->name('accounts.store');
Route::get('/accounts/edit_account/{account_id}', 'AccountController@edit')->name('accounts.edit');
Route::post('/accounts/edit_account/{account_id}', 'AccountController@update')->name('accounts.update');
Route::delete('/accounts/account/{account_id}', 'AccountController@destroy')->name('accounts.destroy');
//Manage courses
Route::get('/courses', 'CourseController@index')->name('courses.index');
Route::get('/courses/show/{id}', 'CourseController@show')->name('courses.show');
Route::get('/courses/showpdf/{id}', 'CourseController@showpdf')->name('courses.showpdf');
Route::get('/courses/new_course', 'CourseController@create')->name('courses.create');
Route::post('/courses/new_course', 'CourseController@store')->name('courses.store');
Route::get('/courses/course/{id}', 'CourseController@edit')->name('courses.edit');
Route::post('/courses/course/{id}', 'CourseController@update')->name('courses.update');
Route::delete('/courses/course/{id}', 'CourseController@destroy')->name('courses.destroy');

//Manage Subscriptions
Route::get('/subscriptions', 'SubscriptionController@index')->name('subscriptions.index');
//Manage Subscriptions
Route::get('/orders', 'OrderController@index')->name('orders.index');
//Manage Subscriptions
Route::get('/pnl', 'PnlController@index')->name('pnl.index');
//Manage ironfx
Route::get('/ironfx', 'IronFXController@index')->name('ironfx.index');
//Manage t4trade
Route::get('/t4trade', 'T4TradeController@index')->name('t4trade.index');
//Manage Positions
Route::get('/open_positions', 'PositionController@getOpenPositions')->name('positions.open');
Route::get('/closed_positions', 'PositionController@getClosedPositions')->name('positions.closed');
//Manage Groups
Route::get('/settings/protections', 'ProtectionController@index')->name('settings.protections');
Route::get('/groups', 'GroupController@index')->name('groups.index');
//Manage Groups
Route::get('/settings', 'SlaveGroupController@index')->name('settings.index');
Route::get('/settings/symbols', 'SymbolSettingController@index')->name('settings.symbols');
//Manage Cleanup
Route::get('/cleanup', 'CleanupAccountController@index')->name('cleanup.index');
Route::put('/cleanup', 'CleanupAccountController@update')->name('cleanup.update');
Route::post('/cleanup', 'CleanupAccountController@execute')->name('cleanup.execute');

//Manage Cleanup
Route::get('/risk-settings', 'RiskSettingsController@index')->name('risk-settings.index');
Route::put('/risk-settings', 'RiskSettingsController@update')->name('risk-settings.update');

// Route for listing available languages
Route::get('/languages', 'LanguageController@index')->name('languages.index');
Route::get('/languages/{locale?}', 'LanguageController@edit')->name('languages.edit');
Route::post('/languages/{locale}', 'LanguageController@update')->name('languages.update');

// Notification Rules Routes
Route::get('/notification-rules', 'NotificationRuleController@index')->name('notification-rules.index');
Route::get('/notification-rules/create', 'NotificationRuleController@create')->name('notification-rules.create');
Route::post('/notification-rules', 'NotificationRuleController@store')->name('notification-rules.store');
Route::get('/notification-rules/{notificationRule}/edit', 'NotificationRuleController@edit')->name('notification-rules.edit');
Route::put('/notification-rules/{notificationRule}', 'NotificationRuleController@update')->name('notification-rules.update');
Route::delete('/notification-rules/{notificationRule}', 'NotificationRuleController@destroy')->name('notification-rules.destroy');

// Email Content Edit
Route::get('admin/notifications/select', 'NotificationController@selectNotification')->name('notifications.select');
Route::post('admin/notifications/redirect', 'NotificationController@redirectToEdit')->name('notifications.redirect');
Route::get('admin/notifications/{notificationType}/{language}/edit', 'NotificationController@edit')->name('notifications.edit');
Route::put('admin/notifications/{notificationType}/{language}', 'NotificationController@update')->name('notifications.update');

Route::get('notification-test', 'NotificationTestController@showForm')->name('notification.test');
Route::post('notification-test/send', 'NotificationTestController@sendNotification')->name('notification.test.send');

// Telegram Settings routes
Route::get('/telegram-settings', 'TelegramSettingsController@index')->name('telegram-settings.index');
Route::post('/telegram-settings/groups', 'TelegramSettingsController@storeGroup')->name('telegram-settings.store-group');
Route::get('/telegram-settings/groups/{group}', 'TelegramSettingsController@getGroup')->name('telegram-settings.get-group');
Route::put('/telegram-settings/groups/{group}', 'TelegramSettingsController@updateGroup')->name('telegram-settings.update-group');
Route::delete('/telegram-settings/groups/{group}', 'TelegramSettingsController@destroyGroup')->name('telegram-settings.destroy-group');
Route::post('/telegram-settings/video', 'TelegramSettingsController@updateVideoLink')->name('telegram-settings.update-video');
Route::get('/telegram-settings/video/{language}', [TelegramSettingsController::class, 'getVideoByLanguage'])
    ->name('telegram-settings.get-video');

Route::post('/users/{user}/forced-access', 'UserController@updateForcedAccess')->name('users.forced-access');

//Manage Affiliates
Route::get('/affiliates', [AffiliateController::class, 'index'])->name('affiliates.index');
Route::post('/affiliates/{user}/payment', [AffiliateController::class, 'updatePayment'])->name('affiliates.update-payment');
Route::post('/affiliates/{user}/referral-amount', [AffiliateController::class, 'updateReferralAmount'])->name('affiliates.update-referral-amount');
Route::post('/affiliates/default-price', [AffiliateController::class, 'updateDefaultReferralPrice'])->name('affiliates.update-default-price');
Route::get('/affiliates/{user}/referrals', [AffiliateController::class, 'getReferrals'])->name('affiliates.referrals');

Route::get('/metatrader', [MetaTraderController::class, 'index'])->name('metatrader.index');
Route::get('/metatrader/{id}', [MetaTraderController::class, 'show'])->name('metatrader.show');
Route::post('/metatrader/{id}/disconnect', [MetaTraderController::class, 'disconnect'])->name('metatrader.disconnect');
Route::post('/metatrader/{id}/connect', [MetaTraderController::class, 'connect'])->name('metatrader.connect');
Route::post('/metatrader/{id}/assign-template', [MetaTraderController::class, 'assignTemplate'])->name('metatrader.assign-template');
Route::post('/metatrader/bulk-action', [MetaTraderController::class, 'bulkAction'])->name('metatrader.bulk-action');
Route::get('/metatrader/{id}/logs', [MetaTraderController::class, 'logs'])->name('metatrader.logs');