<?php

namespace App\Http\Controllers;

use App\Beneficiary;
use App\DataTables\RatesDataTable;
use App\Rate;
use App\RateSetting;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use tests\Mockery\Adapter\Phpunit\EmptyTestCase;

class RatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param RatesDataTable $ratesDataTable
     * @return \Illuminate\Http\Response
     */
    public function index(RatesDataTable $ratesDataTable)
    {
        return $ratesDataTable->render('rates.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Rate  $rate
     * @return \Illuminate\Http\Response
     */
    public function show(Rate $rate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Rate  $rate
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Rate $rate)
    {
        return  view('rates.edit', ['rate' =>$rate]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Rate  $rate
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Rate $rate)
    {
        try{

            Log::info("[RatesController][update]\t called with \t",$request->all());
            $rate->update(['rate' =>$request->input('rate')]);

            $request->session()->flash("success","Rate  Updated Successfully.");
        }catch (\Exception $exception){
            $request->session()->flash("error","Sorry Something Went Wrong");
            Log::info("[RatesController][update]\t Error:  \t".$exception->getMessage());
            Log::info("[RatesController][update]\t Error:  \t".$exception->getTraceAsString());

            return  redirect()->back()->withInput();
        }

        return  redirect()->route('rates.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Rate  $rate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rate $rate)
    {
        //
    }


    public function getUserRates(User $user){

    }

    public static function getLatestRate(Rate $rate){
        try
        {
            Log::info("[[RatesController]][getLatestRate]\tcalled for  ",$rate->toArray());

            $latestRate = \Redis::hget('__stp__system_rates',$rate->source_currency."_".$rate->destination_currency);
            if (empty($latestRate))
            {
                $baseEndPoint = env('EXCHANGE_RATES_BASE_ROUTE');
                $baseEndPoint = str_replace("@@source@@",$rate->source_currency,$baseEndPoint);
                $baseEndPoint = str_replace("@@destination@@",$rate->destination_currency,$baseEndPoint);

                Log::info("[[RatesController]][getLatestRate]\tAbout Calling  ".$baseEndPoint);

                $httpClient = new Client();
                $httpResponse = $httpClient->get($baseEndPoint);


                Log::info("[[RatesController]][getLatestRate]\tHTTP Response STATUS Code ".$httpResponse->getStatusCode());
                Log::info("[[RatesController]][getLatestRate]\tHTTP Response  ".$httpResponse->getBody());

                $httpResponse =  json_decode($httpResponse->getBody(),true);

                if (!empty($httpResponse) && is_array($httpResponse) && array_key_exists('success',$httpResponse) && $httpResponse['success']){
                    if (array_key_exists('quotes',$httpResponse)){
                        $quote = $httpResponse['quotes'];
                        $quoteKey = strtoupper(trim($rate->source_currency.$rate->destination_currency));
                        if (array_key_exists($quoteKey,$quote)){
                            $latestRate = $quote[$quoteKey];
                            Log::info("[[RatesController]][initializeAllConfiguredRates]\tApplying Mark Up. Rate Before: ".$latestRate);

                            $markedUpRate = self::applyRateMarkUp($rate,$latestRate);
                            Log::info("[[RatesController]][initializeAllConfiguredRates]\tApplying Mark Up. Rate After: ".$markedUpRate);

                            \Redis::hset('__stp__system_rates',$rate->source_currency."_".$rate->destination_currency,$markedUpRate);
                        }
                    }
                }
            }

            return $latestRate;
        }catch (ServerException $exception)
        {
            Log::error("[[RatesController]][getLatestRate]\tServer Exception Error: ".$exception->getResponse()->getBody()->getContents());
        }catch (ClientException $exception)
        {
            Log::error("[[RatesController]][getLatestRate]\tClient Exception Error: ".$exception->getResponse()->getBody()->getContents());
        }catch (RequestException $exception)
        {
            Log::error("[[RatesController]][getLatestRate]\tRequest Exception Error: ".$exception->getResponse()->getBody()->getContents());
        }catch (\Exception $exception)
        {
            Log::error("[[RatesController]][getLatestRate]\tError Getting Latest Rates... Error: ".$exception->getMessage());
            Log::error($exception->getTraceAsString());
        }
        return null;
    }

    public static function setSystemBaseRates($sourceCurrency, $date = null)
    {
        try
        {
            if (empty($sourceCurrency)){
                $sourceCurrency = "USD";
            }

            try{
                $date = Carbon::parse($date);
            }catch (\Exception $exception){

                $date = null;
            }


            $destinationCurrencies = \App\RateSetting::where('source_currency','!=',$sourceCurrency)
                ->where('destination_currency','!=',$sourceCurrency)->distinct('destination_currency')
                ->pluck('destination_currency')->toArray();

            Log::info("[[RatesController]][setSystemBaseRates]\tcalled for  ",
                (!empty($destinationCurrencies) ? $destinationCurrencies : []));

            if (!empty($destinationCurrencies))
            {
                $baseEndPoint = env('EXCHANGE_RATES_BASE_ROUTE');
                $baseEndPoint = str_replace("@@source@@",$sourceCurrency,$baseEndPoint);
                $baseEndPoint = str_replace("@@destination@@",implode(",",$destinationCurrencies),$baseEndPoint);

                if (!empty($date)){
                    $baseEndPoint .= "&date=".$date->format("Y-m-d");
                }

                Log::info("[[RatesController]][setSystemBaseRates]\tAbout Calling  ".$baseEndPoint);

                $httpClient = new Client();
                $httpResponse = $httpClient->get($baseEndPoint);


                Log::info("[[RatesController]][setSystemBaseRates]\tHTTP Response STATUS Code ".$httpResponse->getStatusCode());
                Log::info("[[RatesController]][setSystemBaseRates]\tHTTP Response  ".$httpResponse->getBody());

                $httpResponse =  json_decode($httpResponse->getBody(),true);

                if (!empty($httpResponse) && is_array($httpResponse) && array_key_exists('success',$httpResponse) && $httpResponse['success'])
                {
                    if (array_key_exists('quotes',$httpResponse))
                    {
                        $quotes = $httpResponse['quotes'];
                        foreach ($quotes as $key => $quote)
                        {
//                            $key = str_replace($sourceCurrency,$sourceCurrency."_",$key);

                            Log::info("[[RatesController]][setSystemBaseRates]\tAbout To Resolve Rate Object  ");

                            if (empty($date)){
                                $date = now();
                            }


                            $destination_currency = str_replace($sourceCurrency, "", $key);

                            Log::info("[[RatesController]][setSystemBaseRates]\tSource: ".$sourceCurrency.", destination: ".$destination_currency.", date: ".$date->format("Y-m-d"));
                            $rate = \App\Rate::where(['source_currency' => $sourceCurrency, 'destination_currency' => $destination_currency])->whereDate('created_at',$date->format('Y-m-d'))->first();

                            if (empty($rate))
                            {
                                Log::info("[[RatesController]][setSystemBaseRates]\tCreating new rate object  Source: ".$sourceCurrency.", destination: ".$destination_currency);
                                $rate = \App\Rate::create(['source_currency' => $sourceCurrency, 'destination_currency' => $destination_currency, 'rate' => $quote, 'created_at' => $date->format('Y-m-d')]);
                            }

                            Log::info("[[RatesController]][setSystemBaseRates]\tDone. Rate Object Found  ",$rate->toArray());

                            Log::info("[[RatesController]][setSystemBaseRates]\tApplying Mark Up. Rate Before: ".$quote);

                            $markedUpRate = self::applyRateMarkUp($rate,$quote);
                            Log::info("[[RatesController]][setSystemBaseRates]\tApplying Mark Up. Rate After: ".$markedUpRate);

//                            \Redis::hset('__stp__system_rates',$key,$markedUpRate);

                        }
                        return "OK";
                    }
                }
            }

            return null;
        }catch (ServerException $exception)
        {
            Log::error("[[RatesController]][setSystemBaseRates]\tServer Exception Error: ".$exception->getResponse()->getBody()->getContents());
        }catch (ClientException $exception)
        {
            Log::error("[[RatesController]][setSystemBaseRates]\tClient Exception Error: ".$exception->getResponse()->getBody()->getContents());
        }catch (RequestException $exception)
        {
            Log::error("[[RatesController]][setSystemBaseRates]\tRequest Exception Error: ".$exception->getResponse()->getBody()->getContents());
        }catch (\Exception $exception)
        {
            Log::error("[[RatesController]][setSystemBaseRates]\tError Getting Latest Rates... Error: ".$exception->getMessage());
            Log::error($exception->getTraceAsString());
        }
        return null;
    }

    public static function mimicBaseRates()
    {
        try
        {
            Log::info("[[RatesController]][mimicBaseRates]\tCalled ...");
            $rateSettings = RateSetting::all();

            Log::info("[[RatesController]][mimicBaseRates]\tGot All Rate Setings ...",(!empty($rateSettings) ? $rateSettings->toArray() : []));
            foreach ($rateSettings as $rateSetting)
            {
                Log::info("[[RatesController]][mimicBaseRates]\tGetting Rate For setting... ",$rateSetting->toArray());
                $rate = \App\Rate::where(['source_currency' => $rateSetting->source_currency, 'destination_currency' =>
                    $rateSetting->destination_currency])->whereDate('created_at',Carbon::now()->format('Y-m-d'))->first();

                if (empty($rate))
                {
                    Log::info("[[RatesController]][mimicBaseRates]\tRate is empty... creating new rate... ",$rateSetting->toArray());

                    $rate = \App\Rate::create(['source_currency' => $rateSetting->source_currency, 'destination_currency' => $rateSetting->destination_currency])->first();

                    Log::info("[[RatesController]][mimicBaseRates]\tdone creating rate ",$rateSetting->toArray());

                }

                if ($rate->source_currency == $rate->destination_currency)
                {
                    Log::info("[[RatesController]][mimicBaseRates]\tSource/destination currencies are same.. exiting..  ",$rateSetting->toArray());

                    $inferredRate = 1;
                }else
                {
//                    $proxySourceCurrencyRate = \App\Rate::where(['source_currency' => 'USD', 'destination_currency' => $rateSetting->source_currency])->first();
//                    $proxyDestinationCurrencyRate = \App\Rate::where(['source_currency' => 'USD', 'destination_currency' => $rateSetting->destination_currency])->first();
//                    $inferredRate = $proxyDestinationCurrencyRate->rate/$proxySourceCurrencyRate->rate;

                    Log::info("[[RatesController]][mimicBaseRates]\tgetting latest rates  ",$rateSetting->toArray());
                    $inferredRate = self::getLatestRate($rate);
                    Log::info("[[RatesController]][mimicBaseRates]\tdone  ",$rateSetting->toArray());

                }

                Log::info("[[RatesController]][mimicBaseRates]\tApplying Mark Up. Rate Before: ".$inferredRate);
                $markedUpRate = self::applyRateMarkUp($rate,$inferredRate);
                Log::info("[[RatesController]][mimicBaseRates]\tApplying Mark Up. Rate After: ".$markedUpRate);

                $key = $rateSetting->source_currency."_".$rateSetting->destination_currency;
                \Redis::hset('__stp__system_rates',$key,$markedUpRate);

            }
        }catch (\Exception $exception){

        }
    }

    private static function applyRateMarkUp(Rate $rate, $newRate){
        $rateSetting = \App\RateSetting::where(['source_currency' => $rate->source_currency, 'destination_currency' => $rate->destination_currency])->first();

        if (!empty($rate))
        {
            if (!empty($rateSetting))
            {
                if (!empty($rateSetting->markup_percentage)){
                    $newRate += $newRate * ($rateSetting->markup_percentage/100);
                }

                if (!empty($rateSetting->markup_fixed)){
                    $newRate += $rateSetting->markup_fixed;
                }
            }
        }
        $rate->rate = $newRate;
        $rate->save();
        return $newRate;
    }


    public function resolveRatesByBeneficiary(User $user, Beneficiary $beneficiary){
        $rate = \App\Rate::where(['source_currency' => $user->country->currency_code, 'destination_currency' => $beneficiary->country->currency_code])->latest()->first();

        if (!empty($rate)){
            return $rate;
        }
        return [];
    }

}
