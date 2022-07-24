<?php

use App\Beneficiary;
use App\Rate;
use App\User;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['prefix' => 'utils/broadcasts/fake-events/'],function (){
    Route::post('assign-accounts/', 'Api\BroadcastController@accountAssignedBroadcast')
        ->name('broadcast.faker.account-assignment');
    Route::post('e-value-updates/', 'Api\BroadcastController@eValueUpdateBroadcast')
        ->name('broadcast.faker.e-value-updates');
    Route::post('transactions/{transaction}', 'Api\BroadcastController@transactionBroadcast')
        ->name('broadcast.faker.transactions');
});


Route::delete('/user/{msisdn}/purge/', 'Api\UsersController@purgeUser');
Route::get('/user/resolve/{accountNumber}', 'UsersController@resolveUserByStarPayAccount');

Route::post('transactions/callback/partners/zeepay/{transaction}',
    'Api\TransactionsController@handleZeepayPaymentCallBack')->name('zeepay.transactions.callback');
Route::post('transactions/callback/partners/apps-mobile/{transaction}',
    'Api\TransactionsController@handleAppsMobileCallBack')->name('apps-mobile.transactions.callback');
Route::post('payments/callback/partners/pay-stack/','PaymentsController@handlePayStackCallBack')
    ->name('payments.paystack.callback');
Route::get('banks/{countryCode}','BanksController@getBankByCountryCode');
Route::any('payments/callback/stripe','PaymentsController@handleStripePaymentCallBack')
    ->name('payments.callback.stripe');
Route::get('resolve-rates/{user}/{beneficiary}','RatesController@resolveRatesByBeneficiary');





Route::group(['prefix' => 'v1'],function (){


    //authentication related APIs
    Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
        Route::post('login', 'Api\AuthController@login');
        Route::post('logout', 'Api\AuthController@logout');
        Route::post('refresh', 'Api\AuthController@refresh');
//        Route::post('me', 'AuthController@me');
    });


    //User Model APIs
    Route::group(['middleware' => 'api',], function ($router) {
        Route::get('charges', 'Api\ChargesController@index')->name('charges.index');
        Route::post('charges/resolve', 'Api\ChargesController@resolveCharge')->name('charges.resolve');
        Route::post('users/authenticate', 'Api\UsersController@authenticate')->name('user.authenticate');
        Route::post('user/{user}/do-account-top-up', 'Api\UsersController@doAccountTopUp')->name('user.doAccountTopUp');
        Route::post('user/{user}/do-account-debit', 'Api\UsersController@doAccountDebit')->name('user.doAccountDebit');
        Route::get('users/match/account-number-pattern/{accountNumber}',
            'Api\UsersController@getUserByAccountNumberMatch')->name('user.find-by-account-number-match');
        Route::get('users', 'Api\UsersController@index')->name('user.index');
        Route::post('user', 'Api\UsersController@store')->name('user.store');
        Route::get('user/{user}', 'Api\UsersController@show')->name('user.show');
        Route::patch('user/{user}', 'Api\UsersController@update')->name('user.update');
        Route::delete('user/{user}', 'Api\UsersController@destroy')->name('user.destroy');
    });


    //Configuration APIs
    Route::group(['middleware' => 'api','prefix' => 'system-configs'], function ($router) {
        Route::get('country/{countryId}/services', 'Api\ConfigurationsController@getSupportedServicesByCountry')
            ->name('configs.get-supported-services-by-country');

        Route::get('services/{service}/countries', 'Api\ConfigurationsController@getSupportCountriesByServiceName')
            ->name('configs.get-supported-countries-by-service-name');

        Route::get('supported-countries', 'Api\ConfigurationsController@getSupportedCountries')
            ->name('configs.get-supported-countries');

        Route::get('supported-services', 'Api\ConfigurationsController@getSupportedServices')
            ->name('configs.get-supported-services');




        Route::get('rates/latest', 'Api\ConfigurationsController@getLatestRates')->name('configs.latest-rates');

        Route::get('rates/{date}', 'Api\ConfigurationsController@getRatesByDate')->name('configs.get-rates-by-date');

        Route::get('rate/{sourceCurrency}/{destinationCurrency}', 'Api\ConfigurationsController@getRate')
            ->name('configs.get-rate-by-currency');
    });

    //Beneficiary Model APIs
    Route::group(['middleware' => 'api','prefix' => 'beneficiaries'], function ($router) {
        Route::get('/{beneficiary}', 'Api\BeneficiariesController@show')->name('beneficiaries.show');
        Route::patch('/{beneficiary}', 'Api\BeneficiariesController@update')->name('beneficiaries.update');
        Route::delete('/{beneficiary}', 'Api\BeneficiariesController@destroy')->name('beneficiaries.destroy');
        Route::get('/', 'Api\BeneficiariesController@index')->name('beneficiaries.index');
        Route::get('get-by-user/{user}', 'Api\BeneficiariesController@getUserBeneficiaries')
            ->name('beneficiaries.get-by-user');
        Route::post('/', 'Api\BeneficiariesController@store')->name('beneficiaries.store');

    });


    Route::group(['middleware' => 'api','prefix' => 'transactions'], function ($router) {


        Route::post('/user/{user}/beneficiary/{beneficiary}/rate/{rate}', 'Api\TransactionsController@store')
            ->name('transactions.store');
        Route::get('/{transaction}', 'Api\TransactionsController@show')->name('transactions.show');
//        Route::patch('/{transactions}', 'Api\TransactionsController@update')->name('transactions.update');
//        Route::delete('/{transactions}', 'Api\TransactionsController@destroy')->name('transactions.destroy');
        Route::get('/', 'Api\TransactionsController@index')->name('transactions.index');
        Route::get('get-by-user/{user}', 'Api\TransactionsController@getUserTransactions')
            ->name('beneficiaries.get-by-user');
//        Route::post('/', 'Api\TransactionsController@store')->name('beneficiaries.store');

    });

//Bank Model APIs
    Route::group(['middleware' => 'api','prefix' => 'banks'], function ($router) {
        Route::get('/', 'Api\BanksController@index')->name('banks.index');
        Route::get('/{countryId}', 'Api\BanksController@getSupportedServicesByCountry')->name('banks.by-country');
    });


//Billers Model APIs
    Route::group(['middleware' => 'api','prefix' => 'billers'], function ($router) {
        Route::get('/', 'Api\BillersController@index')->name('billers.index');

        Route::get('/airtime/get-allowed-packages/{countryCode}/{msisdn}',
            'Api\BillersController@getAllowedAirtimePackages')->name('billers.get-airtime-packages');

        Route::get('/{countryId}', 'Api\BillersController@getBillersByCountry')->name('billers.by-country');
        Route::post('/user/{user}/biller/{billerId}', 'Api\BillersController@validateBillPayment')
            ->name('billers.validation');
        Route::post('/user/{user}/beneficiary/{beneficiary}/rate/{rate}/biller/{biller}/reference/{reference}',
            'Api\BillersController@completeBillPaymentTransaction')->name('billers.complete-payment');
    });

});