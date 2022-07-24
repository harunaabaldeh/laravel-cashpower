<?php


namespace App\Utils;


use App\Rate;
use App\Transaction;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class Stripe
{
    public static function createCheckout(Transaction $transaction){
        Log::debug("[Stripe][createCheckout][".$transaction->id."]\t...called for ... ",$transaction->toArray());

        try{

            $session = null;
            if (empty($transaction->reference)){
                $reference = Uuid::uuid4()->toString();
                $transaction->reference = $reference;
                $transaction->save();
            }

            $rate = Rate::where(['source_currency' => $transaction->source_currency, 'destination_currency' => 'USD'])->latest()->first();

            if (!empty($rate)){
                \Stripe\Stripe::setApiKey(env('srtipe.sk'));
                $amount = intval(round(($transaction->source_amount * $rate->rate), 0, PHP_ROUND_HALF_UP));
                $params = [
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'name' => 'Funds Transfer',
                        'description' => 'Payment For funds transfer to your beneficiary ' .
                            (!empty($transaction->beneficiary) ? $transaction->beneficiary->fullname : ""),
                        'images' => ['http://via.placeholder.com/240X90'],
                        'amount' => $amount * 100,
                        'currency' => 'usd',
                        'quantity' => 1,
                    ]],

                    'success_url' => route('payments.stripe.redirect', ['success', "session_id" => $transaction->reference]),
                    'cancel_url' => route('payments.stripe.redirect', ['cancel']),
                ];

                Log::info("[Stripe][createCheckout][".$transaction->id."]\t...",$params);

                $session = \Stripe\Checkout\Session::create($params);

                $sessionDetails = $session->toArray();

                if (!empty($sessionDetails) && is_array($sessionDetails) && array_key_exists('payment_intent',$sessionDetails)){

                    Log::info("[Stripe][createCheckout][".$transaction->id."]\t...Session Object\t",$sessionDetails);

                    $transaction->paymentIntent = $sessionDetails['payment_intent'];
                    $transaction->save();
                }


            }

        }catch (\Exception $exception){
            Log::error("[Stripe][createCheckout][".$transaction->id."]\t...".$exception->getMessage());
            Log::error("[Stripe][createCheckout][".$transaction->id."]\t...".$exception->getTraceAsString());
        }

        return (!empty($session) && !empty($session->id)) ? $session->id : null;
    }
}