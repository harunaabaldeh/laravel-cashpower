<?php

namespace App\Http\Controllers;

use App\Fund;
use App\Jobs\TransactionsDispatcher;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class PaymentsController extends Controller
{
    public function getAccountTopUpPage(){
        return view('top-up.create',['destination_currency' => Auth::user()->country->currency_code]);
    }


    public function createPaymentObject(Request $request){
        try{

            $user = Auth::user();
            $sourceAmount = $request->input('amount');
            $rate = \App\Rate::where(['source_currency' => $user->country->currency_code, 'destination_currency'
            => "NGN"])->latest()->first();

            $destinationAmount = round(($sourceAmount * $rate->rate),2,PHP_ROUND_HALF_UP);

            if (!empty($rate))
            {
                $payment = \App\Payment::create(['reference' => Uuid::uuid4()->toString(),'source_currency' => $user->country->currency_code,
                    'destination_currency' => "NGN",'source_amount' => $sourceAmount,'destination_amount' => $destinationAmount,
                    'user_id' => $user->id,'rate_id' => $rate->id,'processor' => "PayStack"]);

                $payStack = new \Yabacon\Paystack(env("PAY_STACK_KEY"));

                try
                {

                    $transaction = $payStack->transaction->initialize([
                        'amount'=> $payment->source_amount * 100,
                        'email'=> $user->email,
                        'reference'=>$payment->reference,
                        'currency' => $payment->source_currency,
                    ]);

                    return redirect($transaction->data->authorization_url);
                } catch(\Yabacon\Paystack\Exception\ApiException $e){

                    Log::error("[PaymentsController][createPaymentObject]\t Error: ".$e->getMessage());
                    Log::error("[PaymentsController][createPaymentObject]\t Error: ".$e->getTraceAsString());


                    if (Str::contains($e->getMessage(),"Currency not supported")){

                        $transaction = $payStack->transaction->initialize([
                            'amount'=> $destinationAmount * 100,
                            'email'=> $user->email,
                            'reference'=>$payment->reference,
                        ]);
                        return redirect($transaction->data->authorization_url);
                    }


                    $request->session()->flash("error","Payment Transaction Bridge Failure");
                }

            }else
            {
                $request->session()->flash("error","Rate Resolution Failed");
            }

        }catch (\Exception $exception){
            Log::error("[PaymentsController][createPaymentObject]\t Error: ".$exception->getMessage());
            Log::error("[PaymentsController][createPaymentObject]\t Error: ".$exception->getTraceAsString());

        }

        return  redirect()->back()->withInput();
    }


    public function initiatePayment(Transaction $transaction){
        return view('payments.initiate',['checkOutSessionId' => $transaction->reference]);
    }

    public function stripeRedirect($redirectType, Request $request){
        Log::alert("[PaymentsController][stripeRedirect]\t Transaction Reference\t".$redirectType);
        Log::alert("[PaymentsController][stripeRedirect]\t headers\t",$request->header());
        Log::alert("[PaymentsController][stripeRedirect]\tRequest Body \t".$request->getContent());

       if ($redirectType == "success"){
           $request->session()->flash("success","Payment Completed Successfully. Your Transaction will be initiated Shortly.");
       }

       if ($redirectType == "cancel"){
           $request->session()->flash("error","Your payment was cancelled");
       }
        return redirect()->route('home');
    }

    public function handleStripePaymentCallBack(Request $request){
        Log::alert("[PaymentsController][handlePaymentCallBack]\t headers\t",$request->header());
        Log::alert("[PaymentsController][handlePaymentCallBack]\tRequest Body \t"
            .str_replace("\n","",$request->getContent()));

        $requestBody = json_decode($request->getContent(),true);

        if (!empty($requestBody) && is_array($requestBody))
        {
            Log::alert("[PaymentsController][handlePaymentCallBack]\tRequest Body After Conversion\t"
                ,$requestBody);

            $transaction = null;
            $paymentData = array();
            if (array_key_exists('data',$requestBody) && array_key_exists('object',$requestBody['data'])){
                $paymentData = $requestBody['data']['object'];
                if (array_key_exists('payment_intent',$paymentData) && !empty($paymentData['payment_intent'])){
                    $transaction = \App\Transaction::whereJsonContains('properties->paymentIntent',
                        $paymentData['payment_intent'])->first();
                }
            }

            if (!empty($transaction))
            {
                Log::alert("[PaymentsController][handlePaymentCallBack]\tTransaction Found\t",
                    $transaction->toArray());

                if (array_key_exists('type',$requestBody) & !empty($requestBody['type'])){

                    if ($requestBody['type'] == "checkout.session.completed"){
                        dispatch(new TransactionsDispatcher($transaction));
//                        $transaction->payment_message = "Payment Success";
//                        $transaction->payment_status = "Success";
//                        $transaction->save();
                    }


                    if ($requestBody['type'] == "charge.failed"){
                        $message = (array_key_exists('failure_message', $paymentData) &&
                            !empty($paymentData['failure_message'])) ? $paymentData['failure_message'] :
                            "Payment Declined";

//                        $transaction->payment_message = $message;
                        $transaction->status_message = $message;
//                        $transaction->payment_status = "Failed";
                        $transaction->status = "Error";
                        $transaction->save();
                    }

                }
            }else{
                Log::error("[PaymentsController][handlePaymentCallBack]\tCan't resolve a transaction object... 
                exiting\t",$requestBody);
            }


        }else
        {
            Log::error("[PaymentsController][handlePaymentCallBack]\tInvalid Request Body... exiting\t");
        }



        return response('',200);
    }

    public function handlePayStackCallBack(Request $request){

        try{
            $requestBody = json_decode($request->getContent(),true);

            if (!empty($requestBody) && is_array($requestBody) && array_key_exists('event',$requestBody)){

                if (array_key_exists('data',$requestBody) && is_array($requestBody['data'])){

                    if (array_key_exists('reference',$requestBody['data'])){
                        $payment = \App\Payment::where('reference',$requestBody['data']['reference'])->first();

                        if (!empty($payment)){

                            if ($requestBody['event'] == "charge.success"){

                                $user = $payment->user;

                                $payment->gateway_id = (array_key_exists('id',$requestBody['data']) && !empty($requestBody['data']['id'])) ? $requestBody['data']['id'] : "";
                                $payment->status = (array_key_exists('status',$requestBody['data']) && !empty($requestBody['data']['status'])) ? $requestBody['data']['status'] : "success";
                                $payment->status = (array_key_exists('status_message',$requestBody['data']) && !empty($requestBody['data']['status_message'])) ? $requestBody['data']['status_message'] : "Payment Processed Successfully";
                                $payment->save();
                                $message = "Account Top Up. of ".$payment->source_currency.": ".$payment->source_amount
                                    . " completed successfully to star pay account-number ".$user->star_account_number;
                                UtilitiesController::creditUserWallet($user,$payment->source_amount,$message,null,$payment->id);
                            }
                        }
                    }
                }
            }
        }catch (\Exception $exception){

        }
    }
}
