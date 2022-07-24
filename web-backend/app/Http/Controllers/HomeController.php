<?php

namespace App\Http\Controllers;

use App\Rate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $transactions = $user->transactions()->latest()->limit(5)->get();

        $ratesArray = $datesArray = array();
        $rates = Rate::where(['source_currency' => 'USD', 'destination_currency' => $user->country->currency_code])
            ->where('created_at','>=',Carbon::now()->subDays(12))->get();

        foreach ($rates as $rate){
            array_push($ratesArray,$rate->rate);
            array_push($datesArray,Carbon::parse($rate->created_at)->format('jS M'));
        }

        $transactionVolumeArray = $transactionVolumeDates = array();
        $transactionVolumeCollections = $user->transactions()->latest()->limit(12)->get();

        foreach ($transactionVolumeCollections as $transactionVolumeCollection){
            array_push($transactionVolumeArray,$transactionVolumeCollection->source_amount);
            array_push($transactionVolumeDates,Carbon::parse($transactionVolumeCollection->created_at)
                ->format('jS M'));
        }

        $uniqueUserTransactionalCurrencies = $user->transactions()->distinct()->pluck('destination_currency')->toArray();

        $receiveAmountDistributions = array();
        foreach ($uniqueUserTransactionalCurrencies as $uniqueUserTransactionalCurrency){
            array_push($receiveAmountDistributions,$user->transactions()
                ->where('destination_currency',$uniqueUserTransactionalCurrency)->sum('source_amount'));
        }

        return view('home',['receiveAmountDistributions' => $receiveAmountDistributions,
            'uniqueUserTransactionalCurrencies' => $uniqueUserTransactionalCurrencies,
            'transactionVolumeArray' => $transactionVolumeArray,'transactionVolumeDates' => $transactionVolumeDates,
            'transactions' => $transactions,'user_currency' => $user->country->currency_code, 'rates' => $ratesArray,
            'dates' => $datesArray]);
    }
}
