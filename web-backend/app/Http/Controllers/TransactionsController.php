<?php

namespace App\Http\Controllers;

use App\Beneficiary;
use App\DataTables\TransactionsDataTable;
use App\Http\Requests\CreateIntraStarPayTransactionRequest;
use App\Jobs\TransactionsDispatcher;
use App\Transaction;
use App\Utils\Zeepay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param TransactionsDataTable $dataTable
     * @return void
     */
    public function index(TransactionsDataTable $dataTable)
    {
        return $dataTable->render('transactions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return void
     */
    public function create(Request $request)
    {
        $request->session()->flash("error","Invalid Params");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $serviceType
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function createByServiceType($serviceType, Request $request)
    {

        $user = \Auth::user();

        if ($serviceType == "star-pay"){

            return view('transactions.create-star-pay',['user' => json_encode($user->toArray()),
                'user_country' => $user->country,'service_type' => $serviceType,]);

        }


        $beneficiaries = \App\Beneficiary::where('user_id', $user->id)->where('account_type',$serviceType)->latest();


        if ($beneficiaries->count() > 0){
            return view('transactions.create',['user' => json_encode($user->toArray()),
                'user_country' => $user->country,'service_type' => $serviceType,
                'beneficiaries' => $beneficiaries->pluck('nickname','id')]);
        }

        $request->session()->flash("error","Kindly Add A Beneficiary To Proceed");
        return  redirect()->route('beneficiaries.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function store(Request $request)
    {
        Log::info("[TransactionsController][store]\t called: \t".$request->getContent());
        try {
            $user = Auth::user();
            $beneficiary = Beneficiary::find($request->input('beneficiary_id'));
            $request->merge(['user_id' => $user->id, 'source_currency' => $user->country->currency_code,
                'destination_currency' => $beneficiary->country->currency_code]);
            $transaction = \App\Transaction::create($request->input());
            $transaction->reference = Uuid::uuid4()->toString();
            $transaction->save();




            if ($user->balance >= $transaction->source_amount)
            {
                dispatch(new TransactionsDispatcher($transaction));
                $request->session()->flash("success","Transaction Dispatched Successfully");
            }else
            {
                \App\Utils\Transaction::updateTransactionStatus($transaction,"Error",
                    "Insufficient Account Balance");

                $request->session()->flash("error","Insufficient Account Balance");
            }

        } catch (\Exception $e) {
            Log::error("[TransactionsController][store]\t Error: ".$e->getMessage());
            Log::error("[TransactionsController][store]\t ".$e->getTraceAsString());
            $request->session()->flash("error","Sorry Something Went Wrong");
            return  redirect()->back()->withInput();
        }

        return redirect()->route('transactions.index');
    }


    /**
     * intraStarPayAccountTransfers
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function intraStarPayAccountTransfers(CreateIntraStarPayTransactionRequest $request)
    {

        Log::info("[TransactionsController][intraStarPayAccountTransfers]\t Request Input ... ",
            $request->input());
        try {


            $user = Auth::user();
            $beneficiary  = \App\User::where('star_account_number',$request->input('star_account_number'))
                ->first();

            $rate = \App\Rate::where(['source_currency' => $user->country->currency_code,
                'destination_currency' => $beneficiary->country->currency_code])->latest()->first();


            if (empty($beneficiary)){
                $request->session()->flash("error","Invalid Star Pay Account");
                return  redirect()->back();
            }

            Log::info("[TransactionsController][intraStarPayAccountTransfers]\tBeneficiary\t",
                $beneficiary->toArray());

            if (empty($rate)){
                $request->session()->flash("error","Rate Resolution Failure");
                return  redirect()->back();
            }


            Log::info("[TransactionsController][intraStarPayAccountTransfers]\tRate\t",$rate->toArray());
            $request->merge(['rate_id' => $rate->id,'user_id' => $user->id,
                'source_currency' => $user->country->currency_code,
                'destination_currency' => $beneficiary->country->currency_code, 'beneficiary_id' => $beneficiary->id]);
            $transaction = \App\Transaction::create($request->input());
            $transaction->reference = Uuid::uuid4()->toString();

            if ($transaction->rate_id == 0){
                $transaction->rate_id = $rate->id;
            }
            $transaction->save();



            if ($user->balance >= $transaction->source_amount)
            {
                UtilitiesController::debitUserWallet($user,$transaction);

                $message = "Star Pay Transfer from # ".$user->star_account_number." of ".
                    $beneficiary->country->currency_code.' '.$transaction->destination_amount;

                UtilitiesController::creditUserWallet($beneficiary,$transaction->destination_amount,
                    $message,$transaction->id,null);

                \App\Utils\Transaction::updateTransactionStatus($transaction,"Success",
                    "Transaction Processed Successfully");


                $request->session()->flash("success","Transaction Dispatched Successfully");
            }else
            {

                \App\Utils\Transaction::updateTransactionStatus($transaction,"Error",
                    "Insufficient Account Balance");

                $request->session()->flash("error","Insufficient Account Balance");
            }

        } catch (\Exception $e) {

            Log::error("[TransactionsController][intraStarPayAccountTransfers]\t Error: ".$e->getMessage());
            Log::error("[TransactionsController][intraStarPayAccountTransfers]\t ".$e->getTraceAsString());
            $request->session()->flash("error","Sorry Something Went Wrong");
            return  redirect()->back()->withInput();
        }

        return redirect()->route('transactions.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }



}
