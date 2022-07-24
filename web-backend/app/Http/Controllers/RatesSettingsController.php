<?php

namespace App\Http\Controllers;

use App\DataTables\RateSettingsDataTable;
use App\RateSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RatesSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param RateSettingsDataTable $rateSettingsDataTable
     * @return \Illuminate\Http\Response
     */
    public function index(RateSettingsDataTable $rateSettingsDataTable)
    {
        return $rateSettingsDataTable->render('rate-settings.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $currencies =\Countries::where('currency','!=', '')->distinct()->pluck('currency','currency_code');
        return  view('rate-settings.create', ['currencies' => $currencies]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try{

            Log::info("[RatesSettingsController][store]\t called with \t",$request->all());
            $rateSetting = RateSetting::where(['source_currency' => $request->input('source_currency'), 'destination_currency' => $request->input('destination_currency')])->first();

            if (!empty($rateSetting)){
                $request->session()->flash("error","Rate Configuration Exists Already!");
            }else{
                RateSetting::create($request->input());
                $request->session()->flash("success","Rate Configuration Created Successfully");
            }

        }catch (\Exception $exception){
            $request->session()->flash("error","Sorry Something Went Wrong");
            Log::info("[RatesSettingsController][store]\t Error:  \t".$exception->getMessage());
            Log::info("[RatesSettingsController][store]\t Error:  \t".$exception->getTraceAsString());

            return  redirect()->back()->withInput();
        }

        return  redirect()->route('rate-settings.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RateSetting  $rateSetting
     * @return \Illuminate\Http\Response
     */
    public function show(RateSetting $rateSetting)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RateSetting  $rateSetting
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(RateSetting $rateSetting)
    {

        $currencies =\Countries::where('currency','!=', '')->distinct()->pluck('currency','currency_code');
        return  view('rate-settings.edit', ['currencies' => $currencies, 'rateSetting' =>$rateSetting]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RateSetting  $rateSetting
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, RateSetting $rateSetting)
    {
        try{

            Log::info("[RatesSettingsController][update]\t called with \t",$request->all());
            $rateSetting->update($request->input());

            $request->session()->flash("success","Rate Configuration Updated Successfully.");
        }catch (\Exception $exception){
            $request->session()->flash("error","Sorry Something Went Wrong");
            Log::info("[RatesSettingsController][update]\t Error:  \t".$exception->getMessage());
            Log::info("[RatesSettingsController][update]\t Error:  \t".$exception->getTraceAsString());

            return  redirect()->back()->withInput();
        }

        return  redirect()->route('rate-settings.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\RateSetting $rateSetting
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(RateSetting $rateSetting,Request $request)
    {

        $rateSetting->delete();
        $request->session()->flash("success","Rate Configuration Removed Successfully");
        return redirect()->route('rate-settings.index');
    }

    public static function createRateSettings($currency)
    {
        $destinationCurrencies = \App\RateSetting::distinct()->pluck('source_currency')->toArray();
        foreach ($destinationCurrencies as $destinationCurrency)
        {
            $rateSetting = \App\RateSetting::where(['source_currency' => $currency,'destination_currency' => $destinationCurrency,]);
            if (empty($rateSetting)){
                \App\RateSetting::create(['source_currency' => $currency,'destination_currency' => $destinationCurrency,]);
            }

            $rate = \App\Rate::where(['source_currency' => $currency,'destination_currency' => $destinationCurrency,]);

            if (empty($rate)){
                \App\Rate::create(['source_currency' => $currency,'destination_currency' => $destinationCurrency,]);
            }

        }
    }
}
