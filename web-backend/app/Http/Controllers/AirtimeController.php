<?php

namespace App\Http\Controllers;

use App\Country;
use App\Fund;
use App\Utils\Stripe;
use App\Utils\Zeepay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Constraint\Count;
use Ramsey\Uuid\Uuid;

class AirtimeController extends Controller
{
    public function create(){
        return  view('airtime.create');
    }

    public function getAllowedPackages(Request $request){
        try {
            $startTime = Carbon::now();

            Log::debug("[AirtimeController][getAllowedPackages]\t... Called..",$request->input());

            $country = $msisdn = null;
            if ($request->has('iso_code')){
                $country = Country::where('iso_3166_2',$request->input('iso_code'))->first();
            }

            if ($request->has('msisdn'))
            {
                $msisdn = $request->input('msisdn');
                if (!empty($msisdn) && is_array($msisdn)){
                    if (array_key_exists('full',$msisdn)){
                        $msisdn = $msisdn['full'];
                    }
                }
            }


            if (!empty($msisdn) && !empty($country))
            {

                if (UtilitiesController::isValidMSISDN($msisdn,$country->iso_3166_2))
                {
                    $msisdn = UtilitiesController::formatMSISDN($msisdn,$country->iso_3166_2);
                    $msisdn = str_replace(" ","",str_replace("+","",$msisdn));
                    Log::debug("[AirtimeController][getAllowedPackages]\t... calling zeepay utility method..");

                    $packageResponse = Zeepay::getAllowedAirtimePackages($country,$msisdn);

                    Log::debug("[AirtimeController][getAllowedPackages]\t...done");
                    if (!empty($packageResponse) && is_array($packageResponse)){
                        Log::debug("[AirtimeController][getAllowedPackages]\t... Packages Received..",
                            $packageResponse);
                    }
                }else
                {
                    Log::debug("[AirtimeController][getAllowedPackages]\t... invalid msisdn ..");
                }

            }else
            {
                Log::debug("[AirtimeController][getAllowedPackages]\t... country or msisdn missing ..",
                    $request->input());
            }

        }catch (\Exception $exception){
            Log::error("[AirtimeController][getAllowedPackages]\t... Exception..\t".$exception->getMessage());
            $request->session()->flash("error","Sorry Something went wrong");
        }

        $endTime = Carbon::now();
        Log::debug("[AirtimeController][getAllowedPackages]\t... Time To Run..".
            ($endTime->diffInSeconds($startTime)));

        if (!empty($packageResponse) && is_array($packageResponse)){
            $user = \Auth::user();

            $rate = \App\Rate::where(['source_currency' => $user->country->currency_code,
                'destination_currency' => $country->currency_code])->latest()->first();
            if (empty($rate)){
                $request->session()->flash("error","Rate Resolution failed. Please try later");
            }
            $packageResponse['rate'] = $rate->rate;
            $packageResponse['rate_id'] = $rate->id;
            $packageResponse['source_currency'] = $user->country->currency_code;
            $packageResponse['name'] = $request->input('name');
            $packageResponse['destination_country_id'] = $country->id;
            return view('airtime.package-selection',$packageResponse);
        }else{
            return redirect()->back()->withInput();
        }
    }

    public function createAirtimeTransaction(Request $request){
        try{
            Log::debug("[AirtimeController][createAirtimeTransaction]\t... Called..",$request->input());

            $startTime = Carbon::now();
            $user = Auth::user();
            $request->merge(['user_id' => $user->id,'beneficiary_id' => 0]);
            $transaction = \App\Transaction::create($request->input());
            $transaction->reference = Uuid::uuid4()->toString();
            $transaction->airtimeMsisdn = $request->input('msisdn');
            $transaction->airtimeReceiverName = $request->input('name');
            $transaction->airtimeReceiverCountry = Country::find($request->input('destination_country_id'))
                ->iso_3166_3;
            $transaction->save();

            dispatch(new \App\Jobs\TransactionsDispatcher($transaction));

            $request->session()->flash("info","Transaction Dispatched");

        }catch (\Exception $exception){

            Log::error("[AirtimeController][createAirtimeTransaction]\t... Exception..\t".
                $exception->getMessage());

            $request->session()->flash("error","Sorry Something Went Wrong");
        }

        $endTime = Carbon::now();
        Log::debug("[AirtimeController][createAirtimeTransaction]\t... Time To Run..".
            ($endTime->diffInSeconds($startTime)));

        return redirect()->route('transactions.airtime.create');
    }
}
