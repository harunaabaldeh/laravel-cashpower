<?php

namespace App\Http\Controllers;

use App\AuthenticationToken;
use App\Fund;
use App\Transaction;
use App\User;
use App\Utils\BroadcastUtil;
use Dompdf\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use Twilio\Rest\Client;

class UtilitiesController extends Controller
{

    private static function broadcastEValueChangeActivity(User  $user, $event, $amount, $balanceBefore, $balanceAfter)
    {
        BroadcastUtil::doBroadCast("user-".$user->msisdn,$event,['user' => $user->toArray(),
            'amount' => $amount, 'balance_before' => $balanceBefore,'balance_after' => $balanceAfter]);
    }

    public static function reverseTransaction(Transaction  $transaction)
    {
        $sender = $transaction->user;

        $message = "Hello ".$sender->firstname.", Your cartis pay transfer from # ".
            $sender->star_account_number." with reference ". $transaction->id.' could not be processed. \r\n'.
            'A a reversal of ' .$transaction->source_currency. $transaction->source_amount.
            " has been done into your account.\r\n";

        UtilitiesController::creditUserWallet($sender,$transaction->source_amount, $message,$transaction->id);
    }

    public static function debitUserWallet(User $user, Transaction $transaction){

        //TODO Send email/sms notification of top-up via \App\Fund Observer

            \DB::raw('lock tables users write');
            $balanceBefore = $user->balance;
            $balanceAfter = round($user->balance - $transaction->source_amount,2,
                PHP_ROUND_HALF_DOWN);

            Fund::create(['type' => 'debit','amount' => $transaction->source_amount,'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,'description' => "Debit Of ".$transaction->source_currency.": "
                    .$transaction->source_amount." For ".$transaction->type." transaction ref: ".$transaction->id,
                'user_id' => $user->id ,'transaction_id' => $transaction->id]);
            $user->balance = $balanceAfter;
            $user->save();
            \DB::raw('unlock tables');

//            dispatch(new \App\Jobs\TransactionsDispatcher($transaction));

            $activity = "";

            if ($transaction->type == "star-pay"){
                $activity = "cartis pay to carts pay transfer";
            }elseif ($transaction->type == "Airtime"){
                $activity = "Airtime Transfer";
            }elseif ($transaction->type == "Wallet"){
                $activity = "cartis pay to Mobile Money";
            }elseif ($transaction->type == "Wallet"){
                $activity = "cartis pay to Cash Pick Up";
            }elseif ($transaction->type == "Bank"){
                $activity = "cartis pay to Bank";
            }

            self::broadcastEValueChangeActivity($user,"user-debit", $transaction->source_amount,$balanceBefore,
                $balanceAfter);
            $message = "Action: Account Debit\r\nAmount: ".$user->country->currency_code." ".$transaction->source_amount
                ."\r\nBalance Before: ".$balanceBefore."\r\nBalance After: ".$balanceAfter."\r\nActivity: ".$activity.
                "\r\nTime: ".now()->format("Y-m-d H:i:s")."\r\nReference: ".$transaction->id."";
        self::sendSMS($message,$user->msisdn);
    }


    public static function debitUserWalletWithoutTransaction(User $user, $amount){

        //TODO Send email/sms notification of top-up via \App\Fund Observer

            \DB::raw('lock tables users write');
            $balanceBefore = $user->balance;
            $balanceAfter = round($user->balance - $amount,2, PHP_ROUND_HALF_DOWN);

            $fund = Fund::create(['type' => 'debit','amount' => $amount,'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,'description' => "Direct Debit from Mobile App ",
                'user_id' => $user->id ]);
            $user->balance = $balanceAfter;
            $user->save();
            \DB::raw('unlock tables');

        self::broadcastEValueChangeActivity($user,"user-debit", $amount,$balanceBefore, $balanceAfter);
//            $activity = "";
//            $message = "Action: Account Debit\r\nAmount: ".$user->country->currency_code." "
//                .$amount."\r\nBalance Before: ".$balanceBefore."\r\nBalance After: ".$balanceAfter."\r\nActivity: ".
//                $activity."\r\nTime: ".now()->format("Y-m-d H:i:s")."\r\nReference: ".rand(100000,999999)."";
//        self::sendSMS($message,$user->msisdn);
        return $fund;
    }


    public static function creditUserWallet(User $user, $creditAmount, $message, $transaction_id = null,
                                            $payment_id = null){
        //TODO Send email/sms notification of top-up via \App\Fund Observer

        \DB::raw('lock tables users write');
        $balanceBefore = $user->balance;
        $balanceAfter = round($user->balance + $creditAmount,2, PHP_ROUND_HALF_DOWN);
        $fundDetails = ['type' => 'credit', 'amount' => $creditAmount, 'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter, 'description' => $message, 'user_id' => $user->id,];

        if (!empty($payment_id)){
            $fundDetails['payment_id'] = $payment_id;
        }

        if (!empty($transaction_id)){
            $fundDetails['transaction_id'] = $transaction_id;
        }

        $fund = Fund::create($fundDetails);
        $user->balance = $balanceAfter;
        $user->save();

        \DB::raw('unlock tables');
        self::broadcastEValueChangeActivity($user,"user-credit", $creditAmount,$balanceBefore, $balanceAfter);
        $message = "Action: Account Credit\r\nAmount: ".$user->country->currency_code." ".$creditAmount.
            "\r\nBalance Before: ".$balanceBefore."\r\nBalance After: ".$balanceAfter."\r\nTime: ".
            now()->format("Y-m-d H:i:s");
        self::sendSMS($message,$user->msisdn);
        return $fund;
    }

    public static function getMNOFromMSISDN($msisdn, $countryCode)
    {
        $mno = null;
        try
        {
            $msisdn = trim($msisdn);
            if (strtoupper(trim($countryCode)) == "GM")
            {
                if (Str::startsWith($msisdn,"+220"))
                {
                    $msisdn = substr($msisdn,4);
                }

                if (Str::startsWith($msisdn,"220"))
                {
                    $msisdn = substr($msisdn,3);
                }

                $msisdnIndex = substr($msisdn,0);

                if (in_array($msisdnIndex,[3,5]))
                {
                    return  "qcell";
                }

                if (in_array($msisdnIndex,[9]))
                {
                    return  "gamcell";
                }

                if (in_array($msisdnIndex,[6]))
                {
                    return  "comium";
                }

                if (in_array($msisdnIndex,[2,7]))
                {
                    return  "africell";
                }
            }

            $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            $carrierMapper = \libphonenumber\PhoneNumberToCarrierMapper::getInstance();
            $mno = trim($carrierMapper->getNameForNumber($phoneUtil->parse($msisdn,$countryCode),$countryCode));
        }catch (\Exception $exception)
        {
        }
        return $mno;
    }

    public static function resolveCountry($countryId){
        $country = null;
        $country = \Countries::find($countryId);
        if (empty($country)){
            $country = \Countries::where('iso_3166_3',$countryId)->orWhere('iso_3166_2',$countryId)->first();
        }
        return $country;
    }
    public static function sendSMS($message, $recipient){
        Log::info("[UtilitiesController][sendSMS]\t Called..");

        if (!Str::startsWith($recipient,"+")){
            $recipient = "+".trim($recipient);
        }

        $client = new Client(env('TWILIO_SID'), env("TWILIO_TOKEN"));
        try{

            $message = $client->messages->create(
                $recipient,
                array(
                    'from' => env("TWILIO_MESSAGE_ORIGINATOR_ALPHA_NUM"),
                    'body' => $message
                )
            );
            Log::debug("[UtilitiesController][sendSMS]\t.. message sid: ".$message->sid);
        }catch (\Exception $exception){


            if (Str::contains($exception->getMessage(),
                " is not currently reachable using the 'From' phone number")){
                $message = $client->messages->create(
                    $recipient,
                    array(
                        'from' => env("TWILIO_MESSAGE_ORIGINATOR"),
                        'body' => $message
                    )
                );
                Log::debug("[UtilitiesController][sendSMS]\t.. message sid: ".$message->sid);
            }else{
                Log::error("[UtilitiesController][sendSMS]\t.. Error: ".$exception->getMessage());
                Log::error("[UtilitiesController][sendSMS]\t.. Trace: ".$exception->getTraceAsString());
            }
        }
    }

    public static  function isValidMSISDN($msisdn,$countryCode = 'GH')
    {
        try
        {
            $phoneUtil = PhoneNumberUtil::getInstance();

//            $phoneUtil->getExampleNumberByType("GH",PhoneNumberType::MOBILE);
            $isValidMSISDN = $phoneUtil->isValidNumber($phoneUtil->parse($msisdn,$countryCode));

            if ($isValidMSISDN){
                return true;
            }
        }catch (NumberParseException $exception)
        {
            Log::error($exception->getTraceAsString());
        }catch (\Exception $exception){
            Log::error($exception->getTraceAsString());
        }

        Log::info("[UtilitiesController][isValidMSISDN]\t falling back on regex");

        $validationRegex = config('custom.wallets.validation.regex.' . $countryCode);

        Log::info("[UtilitiesController][isValidMSISDN]\t regex found ".
            (empty($validationRegex) ? "" : $validationRegex));
        if (!empty($validationRegex)){
            preg_match($validationRegex,$msisdn,$matches);
            return (is_array($matches) && array_key_exists(0,$matches) && count($matches) > 0);

        }


        return false;
    }

    public static  function generateFakeMSISDN($msisdn,$countryCode = 'GH')
    {
        try
        {
            $phoneUtil = PhoneNumberUtil::getInstance();

            $phoneUtil->getExampleNumberByType("GH",PhoneNumberType::MOBILE);
            $isValidMSISDN = $phoneUtil->isValidNumber($phoneUtil->parse($msisdn,$countryCode));

            if ($isValidMSISDN){
                return true;
            }
        }catch (NumberParseException $exception)
        {
            Log::error($exception->getTraceAsString());
        }catch (\Exception $exception){
            Log::error($exception->getTraceAsString());
        }

        Log::info("[UtilitiesController][isValidMSISDN]\t falling back on regex");

        $validationRegex = config('custom.wallets.validation.regex.' . $countryCode);

        Log::info("[UtilitiesController][isValidMSISDN]\t regex found ".
            (empty($validationRegex) ? "" : $validationRegex));
        if (!empty($validationRegex)){
            preg_match($validationRegex,$msisdn,$matches);
            return (is_array($matches) && array_key_exists(0,$matches) && count($matches) > 0);

        }


        return false;
    }

    public static function formatMSISDN($msisdn,$countryCode = 'GH')
    {
        try
        {
            $phoneUtil = PhoneNumberUtil::getInstance();
            return $phoneUtil->format($phoneUtil->parse($msisdn,$countryCode),
                PhoneNumberFormat::INTERNATIONAL);
        }catch (NumberParseException $exception)
        {
            Log::error($exception->getTraceAsString());
        }
        return null;
    }

    public static function generateAndSendOTP(\App\User $user, $password){

        $token = AuthenticationToken::create(['user_id' => $user->id]);
        $token->sendCode();

        \Redis::set("_token_id_credentials_".$token->id,encrypt($password));
        session(["token_id" => $token->id, "user_id" => $user->id, "auth_stage" => "verify_otp"]);

    }
}
