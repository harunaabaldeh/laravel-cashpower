<?php

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

Route::get('/', function () {
    return view('main');
});
Auth::routes(['verify' => true]);
Route::get('verify-authentication-code','Auth\LoginController@getVerify2FAVerificationPage')->name('verify-2fa-view');
Route::post('verify-authentication-code','Auth\LoginController@verify2FAVerification')->name('verify-2fa-verification');



Route::group(['middleware' => 'auth'], function () {

    Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
    Route::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
    Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);

    Route::group(['middleware' => 'kyc.complete'],function (){

        Route::get('/home', 'HomeController@index')->name('home');
        Route::resource('beneficiaries', 'BeneficiariesController');

        Route::group(['middleware' => 'verified'],function (){


            Route::get('/funds','FundsController@index')->name('funds.index');
            Route::get('/accounts/{uuid}/top-up','PaymentsController@getAccountTopUpPage')->name('accounts.top-up');
            Route::post('/accounts/{uuid}/top-up','PaymentsController@createPaymentObject')
                ->name('accounts.top-up.post');
            Route::get('/transactions/airtime/create','AirtimeController@create')->name('transactions.airtime.create');
            Route::post('/transactions/airtime/packages','AirtimeController@getAllowedPackages')
                ->name('transactions.airtime.get-packages');
            Route::post('/transactions/airtime/complete-purchase','AirtimeController@createAirtimeTransaction')
                ->name('transactions.airtime.complete-purchase');
            Route::resource('transactions', 'TransactionsController');

            Route::post('transactions/create/star-pay-transfer', 'TransactionsController@intraStarPayAccountTransfers')
                ->name('transactions.create.star-pay-transfer');
            Route::get('transactions/create/{serviceType}', 'TransactionsController@createByServiceType')
                ->name('transactions.create.by-service-type');

            Route::group(['middleware' => 'is-admin-user'],function (){
                Route::resource('rate-settings', 'RatesSettingsController');
                Route::get('users/fund-user-account/{user}', 'FundsController@getFundingView')->name('users.fund.get');
                Route::post('users/fund-user-account/{user}', 'FundsController@updateUserEValue')
                    ->name('users.fund.post');
                Route::patch('users/{user}/account-status/upgrade/admin', 'UserController@upgradeToAdmin')
                    ->name('users.account-upgrade.admin');
                Route::patch('users/{user}/account-status/upgrade/agent', 'UserController@upgradeToAgent')
                    ->name('users.account-upgrade.agent');
                Route::patch('users/{user}/account-status/de-activate', 'UserController@deActivateUser')
                    ->name('users.account-status.de-activate');
                Route::patch('users/{user}/account-status/re-activate', 'UserController@reActivateUser')
                    ->name('users.account-status.re-activate');
                Route::post('users/store-agent-user', 'UserController@storeAgentUser')->name('users.agent-user.store');
                Route::resource('rates', 'RatesController');
                Route::resource('users', 'UserController');
                Route::resource('charges', 'ChargesController');
            });
        });

        Route::group(['prefix' => 'payments'],function (){
            Route::get('initiate/{transaction}','PaymentsController@initiatePayment')->name('payments.initiate');
            Route::get('stripe/{redirectType}', 'PaymentsController@stripeRedirect')->name('payments.stripe.redirect');
        });
    });

});


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::get('/privacy', function (){
    return response()->file(storage_path("app/public/starpay-privacy-policy.pdf"));
})->name('privacy-policy');

Route::get('/terms-and-conditions', function (){
    return response()->file(storage_path("app/public/starpay-terms-and-conditions.pdf"));
})->name('terms-and-conditions');
