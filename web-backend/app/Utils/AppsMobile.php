<?php


namespace App\Utils;


use App\Http\Controllers\UtilitiesController;
use App\Transaction;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class AppsMobile
{

    //TODO Debit reversals based on 3rd party response
    private static function networkMap($mno){
        $mno = strtolower(trim($mno));

        if (Str::contains($mno,"mtn")){
            return "MTN";
        }


        if (Str::contains($mno,"tigo")){
            return "TIG";
        }

        if (Str::contains($mno,"airtel")){
            return "AIR";
        }

        if (Str::contains($mno,"vodafone")){
            return "VOD";
        }

    }

    private static function getSignature($request_payload){
        return hash_hmac('sha256',json_encode($request_payload),env("APPS_MOBILE_CLIENT_SECRET"));
    }

    public static function checkBalance(){
        try{

            $transaction_time = now();
            $httpClient = new Client();
            $uri = env("APPS_MOBILE_CHECK_BALANCE_END_POINT");

            $request_payload = [
                "trans_type" => "BLC",
                "service_id" => intval(env("APPS_MOBILE_CLIENT_ID")),
                "ts" => $transaction_time->format("Y-m-d H:i:s"),
            ];

            $signature = self::getSignature($request_payload);

            $http_request_params = ['json' => $request_payload, 'headers' => ['Accept' => 'application/json',
                'Authorization' => env("APPS_MOBILE_CLIENT_KEY").':'.$signature,]];

            Log::debug("[AppsMobile][checkBalance][]\t... Request URL.." .$uri, $request_payload);
            Log::debug("[AppsMobile][checkBalance][]\t... signature " .$signature);


            $httpResponse = $httpClient->post($uri,$http_request_params);

            Log::debug("[AppsMobile][checkBalance][]\t... HTTP STATUS..".$httpResponse->getStatusCode());
            Log::debug("[AppsMobile][checkBalance][]\t... HTTP RESPONSE BODY..".$httpResponse->getBody());

//            $httpResponse = json_decode($httpResponse->getBody(),true);

        } catch (ClientException $exception){
        Log::error("[AppsMobile][dispatchMobileMoney]\t... ClientException..\t".
            $exception->getResponse()->getStatusCode());
        Log::error("[AppsMobile][dispatchMobileMoney]\t... ClientException..\t".
            $exception->getResponse()->getBody());
        }catch (RequestException $exception){
            Log::error("[AppsMobile][dispatchMobileMoney]\t... RequestException..\t".
                $exception->getResponse()->getStatusCode());
            Log::error("[AppsMobile][dispatchMobileMoney]\t... RequestException..\t".
                $exception->getResponse()->getBody());
        }catch (\Exception $exception){
            Log::error("[AppsMobile][dispatchMobileMoney]\t... Exception..\t".$exception->getMessage());
            Log::error("[AppsMobile][dispatchMobileMoney]\t... Exception..\t".$exception->getTraceAsString());
        }

    }

    public static function checkTransactionStatus($exttrid){
        try{

            $httpClient = new Client();
            $uri = env("APPS_MOBILE_CHECK_STATUS_END_POINT");

            $request_payload = [
                "exttrid" => $exttrid,
                "trans_type" => "TSC",
                "service_id" => intval(env("APPS_MOBILE_CLIENT_ID")),
            ];

            $signature = self::getSignature($request_payload);

            $http_request_params = ['json' => $request_payload, 'headers' => ['Accept' => 'application/json',
                'Authorization' => env("APPS_MOBILE_CLIENT_KEY").':'.$signature,]];

            Log::debug("[AppsMobile][checkTransactionStatus][]\t... Request URL.." .$uri, $request_payload);
            Log::debug("[AppsMobile][checkTransactionStatus][]\t... signature " .$signature);


            $httpResponse = $httpClient->post($uri,$http_request_params);

            Log::debug("[AppsMobile][checkTransactionStatus][]\t... HTTP STATUS..".
                $httpResponse->getStatusCode());
            Log::debug("[AppsMobile][checkTransactionStatus][]\t... HTTP RESPONSE BODY..".
                $httpResponse->getBody());

//            $httpResponse = json_decode($httpResponse->getBody(),true);

        } catch (ClientException $exception){
        Log::error("[AppsMobile][dispatchMobileMoney]\t... ClientException..\t".
            $exception->getResponse()->getStatusCode());
        Log::error("[AppsMobile][dispatchMobileMoney]\t... ClientException..\t".
            $exception->getResponse()->getBody());
        }catch (RequestException $exception){
            Log::error("[AppsMobile][dispatchMobileMoney]\t... RequestException..\t".
                $exception->getResponse()->getStatusCode());
            Log::error("[AppsMobile][dispatchMobileMoney]\t... RequestException..\t".
                $exception->getResponse()->getBody());
        }catch (\Exception $exception){
            Log::error("[AppsMobile][dispatchMobileMoney]\t... Exception..\t".$exception->getMessage());
            Log::error("[AppsMobile][dispatchMobileMoney]\t... Exception..\t".$exception->getTraceAsString());
        }

    }

    public static function dispatchMobileMoney(Transaction  $transaction){
        try{

            Log::debug("[AppsMobile][dispatchMobileMoney][".$transaction->id."]\t... Called " );
            $transaction_time = now();
            $httpClient = new Client();
            $uri = env("APPS_MOBILE_SEND_REQUEST_END_POINT");


            $reference = substr(str_replace("-","",Uuid::uuid4()->toString()),0,20);

            $user = $transaction->user;
            $beneficiary = \App\Beneficiary::find($transaction->beneficiary_id);

            $transaction->reference = $reference;
            $transaction->save();

            $customer_number = "0" . substr($beneficiary->msisdn, -9);

            $mno = self::networkMap(UtilitiesController::getMNOFromMSISDN($customer_number,"GH"));

            if (empty($mno)){
                \App\Utils\Transaction::updateTransactionStatus($transaction,"Pending",
                    "Pending MNO Rectification");
            }


            if ($customer_number != "0541859113"){
                \App\Utils\Transaction::updateTransactionStatus($transaction,"Error",
                    "Wallet Not whitelisted for testing");
                return;
            }


            $transaction->beneficiary->bank_name = $mno;
            $transaction->beneficiary->save();


            $request_payload = [
                "customer_number" => $customer_number,
                "reference" => $reference,
                "amount" => $transaction->destination_amount,
                "transf_amount" => $transaction->source_amount,
                "exttrid" => $reference,
                "nw" => $mno,
                "trans_type" => "RMT",
                "callback_url" => route('apps-mobile.transactions.callback',$transaction->id),
                "ts" => $transaction_time->format("Y-m-d H:i:s"),
                "sender_number" => $user->msisdn,
                "service_id" => intval(env("APPS_MOBILE_CLIENT_ID")),
                "sender_name" => $user->fullname,
                "recipient_name" => $beneficiary->fullname,
                "sender_gender" => "M",
                "recipient_gender" => "M",
                "ctry_origin_code" => $user->country->iso_3166_3,
                "transf_curr_code" => $transaction->source_currency,
                "recipient_address" => "Ghana",
                "transf_purpose" => "cartis pay transfer - ".$transaction->id,
            ];

            $signature = self::getSignature($request_payload);

            $http_request_params = ['json' => $request_payload, 'headers' => ['Accept' => 'application/json',
                'Authorization' => env("APPS_MOBILE_CLIENT_KEY").':'.$signature,]];

            Log::debug("[AppsMobile][initiateTransaction][".$transaction->id."]\t... Request URL.." .$uri,
                $request_payload);
            Log::debug("[AppsMobile][initiateTransaction][".$transaction->id."]\t... signature " .$signature);


            $httpResponse = $httpClient->post($uri,$http_request_params);

            Log::debug("[AppsMobile][initiateTransaction][".$transaction->id."]\t... HTTP STATUS..".
                $httpResponse->getStatusCode());
            Log::debug("[AppsMobile][initiateTransaction][".$transaction->id."]\t... HTTP RESPONSE BODY..".
                $httpResponse->getBody());

//            $httpResponse = json_decode($httpResponse->getBody(),true);

        } catch (ClientException $exception){
        Log::error("[AppsMobile][dispatchMobileMoney][".$transaction->id."]\t... ClientException..\t".
            $exception->getResponse()->getStatusCode());
        Log::error("[AppsMobile][dispatchMobileMoney][".$transaction->id."]\t... ClientException..\t".
            $exception->getResponse()->getBody());
        }catch (RequestException $exception){
            Log::error("[AppsMobile][dispatchMobileMoney][".$transaction->id."]\t... RequestException..\t".
                $exception->getResponse()->getStatusCode());
            Log::error("[AppsMobile][dispatchMobileMoney][".$transaction->id."]\t... RequestException..\t".
                $exception->getResponse()->getBody());
        }catch (\Exception $exception){
            Log::error("[AppsMobile][dispatchMobileMoney][".$transaction->id."]\t... Exception..\t"
                .$exception->getMessage());
            Log::error("[AppsMobile][dispatchMobileMoney][".$transaction->id."]\t... Exception..\t"
                .$exception->getTraceAsString());
        }

    }

    public static function dispatchBankPayment($amount,$customer_account_number, $sender_name, $receiver_name,
                                               $sort_code){
        try{

            $transaction_time = now();
            $httpClient = new Client();
            $uri = env("APPS_MOBILE_SEND_REQUEST_END_POINT");

            $reference = substr(str_replace("-","",Uuid::uuid4()->toString()),0,20);
            $request_payload = [
                "customer_number" => $customer_account_number,
                "reference" => $reference,
                "amount" => $amount,
                "transf_amount" => $amount,
                "exttrid" => $reference,
                "nw" => "BNK",
                "trans_type" => "RMT",
                "callback_url" => route("apps-mobile.transactions.callback",rand(123)),
                "ts" => $transaction_time->format("Y-m-d H:i:s"),
                "sender_number" => $customer_account_number,
                "service_id" => intval(env("APPS_MOBILE_CLIENT_ID")),
                "sender_name" => $sender_name,
                "recipient_name" => $receiver_name,
                "sender_gender" => "M",
                "recipient_gender" => "F",
                "ctry_origin_code" => "USA",
                "transf_curr_code" => "USD",
                "recipient_address" => "Box MD 1195, Madina - Accra",
                "transf_purpose" => "Test payment",
                "sort_code" => $sort_code
            ];

            $signature = self::getSignature($request_payload);

            $http_request_params = ['json' => $request_payload, 'headers' => ['Accept' => 'application/json',
                'Authorization' => env("APPS_MOBILE_CLIENT_KEY").':'.$signature,]];

            Log::debug("[AppsMobile][dispatchBankPayment][]\t... Request URL.." .$uri, $request_payload);
            Log::debug("[AppsMobile][dispatchBankPayment][]\t... signature " .$signature);


            $httpResponse = $httpClient->post($uri,$http_request_params);

            Log::debug("[AppsMobile][dispatchBankPayment][]\t... HTTP STATUS..".$httpResponse->getStatusCode());
            Log::debug("[AppsMobile][dispatchBankPayment][]\t... HTTP RESPONSE BODY..".$httpResponse->getBody());

//            $httpResponse = json_decode($httpResponse->getBody(),true);

        } catch (ClientException $exception){
        Log::error("[AppsMobile][dispatchBankPayment]\t... ClientException..\t".
            $exception->getResponse()->getStatusCode());
        Log::error("[AppsMobile][dispatchBankPayment]\t... ClientException..\t".
            $exception->getResponse()->getBody());
        }catch (RequestException $exception){
            Log::error("[AppsMobile][dispatchBankPayment]\t... RequestException..\t".
                $exception->getResponse()->getStatusCode());
            Log::error("[AppsMobile][dispatchBankPayment]\t... RequestException..\t".
                $exception->getResponse()->getBody());
        }catch (\Exception $exception){
            Log::error("[AppsMobile][dispatchBankPayment]\t... Exception..\t".$exception->getMessage());
            Log::error("[AppsMobile][dispatchBankPayment]\t... Exception..\t".$exception->getTraceAsString());
        }

    }

    public static function billPayment($amount,$account_number,$mno){
        try{

            $transaction_time = now();
            $httpClient = new Client();
            $uri = env("APPS_MOBILE_SEND_REQUEST_END_POINT");

            $reference = substr(str_replace("-","",Uuid::uuid4()->toString()),0,20);
            $request_payload = [
                "amount" => $amount,
                "exttrid" => $reference,
                "nw" => $mno,
                "trans_type" => "BLP",
                "callback_url" => "https://webhook.site/bcad7e3a-7be0-46e6-85bf-fa674b9192ee",
                "service_id" => intval(env("APPS_MOBILE_CLIENT_ID")),
                "ts" => $transaction_time->format("Y-m-d H:i:s"),
                "account_number" => $account_number,
                "reference" => $reference,
            ];

            $signature = self::getSignature($request_payload);

            $http_request_params = ['json' => $request_payload, 'headers' => ['Accept' => 'application/json',
                'Authorization' => env("APPS_MOBILE_CLIENT_KEY").':'.$signature,]];

            Log::debug("[AppsMobile][billPayment][]\t... Request URL.." .$uri, $request_payload);
            Log::debug("[AppsMobile][billPayment][]\t... signature " .$signature);


            $httpResponse = $httpClient->post($uri,$http_request_params);

            Log::debug("[AppsMobile][billPayment][]\t... HTTP STATUS..".$httpResponse->getStatusCode());
            Log::debug("[AppsMobile][billPayment][]\t... HTTP RESPONSE BODY..".$httpResponse->getBody());

//            $httpResponse = json_decode($httpResponse->getBody(),true);

        } catch (ClientException $exception){
        Log::error("[AppsMobile][billPayment]\t... ClientException..\t".
            $exception->getResponse()->getStatusCode());
        Log::error("[AppsMobile][billPayment]\t... ClientException..\t".
            $exception->getResponse()->getBody());
        }catch (RequestException $exception){
            Log::error("[AppsMobile][billPayment]\t... RequestException..\t".
                $exception->getResponse()->getStatusCode());
            Log::error("[AppsMobile][billPayment]\t... RequestException..\t".
                $exception->getResponse()->getBody());
        }catch (\Exception $exception){
            Log::error("[AppsMobile][billPayment]\t... Exception..\t".$exception->getMessage());
            Log::error("[AppsMobile][billPayment]\t... Exception..\t".$exception->getTraceAsString());
        }

    }


    public static function dispatchAirtime(Transaction  $transaction){
        try{

            Log::debug("[AppsMobile][dispatchAirtime][".$transaction->id."]\t... Called " );
            $airtimeMSISDN = $transaction->beneficiary->msisdn;

            if (Str::startsWith($airtimeMSISDN,"+")){
                $airtimeMSISDN = substr($airtimeMSISDN,1);
            }


            $transaction_time = now();
            $httpClient = new Client();
            $uri = env("APPS_MOBILE_SEND_REQUEST_END_POINT");

            $mno = self::networkMap(UtilitiesController::getMNOFromMSISDN($airtimeMSISDN, "GH"));
            $reference = substr(str_replace("-","",Uuid::uuid4()->toString()),0,20);

            $transaction->beneficiary->bank_name = $mno;
            $transaction->beneficiary->save();


            $transaction->reference = $reference;
            $transaction->save();

            $request_payload = [
                "customer_number" => $airtimeMSISDN,
                "reference" => $reference,
                "amount" => $transaction->destination_amount,
                "transf_amount" => $transaction->source_amount,
                "exttrid" => $reference,
                "nw" => $mno,
                "trans_type" => "ATP",
                "callback_url" => route('apps-mobile.transactions.callback',[$transaction->id]),
                "ts" => $transaction_time->format("Y-m-d H:i:s"),
                "service_id" => intval(env("APPS_MOBILE_CLIENT_ID")),
            ];

            $signature = self::getSignature($request_payload);

            $http_request_params = ['json' => $request_payload, 'headers' => ['Accept' => 'application/json',
                'Authorization' => env("APPS_MOBILE_CLIENT_KEY").':'.$signature,]];

            Log::debug("[AppsMobile][dispatchAirtime][".$transaction->id."]\t... Request URL.." .$uri,
                $request_payload);
            Log::debug("[AppsMobile][dispatchAirtime][".$transaction->id."]\t... signature " .$signature);


            $httpResponse = $httpClient->post($uri,$http_request_params);

            Log::debug("[AppsMobile][dispatchAirtime][".$transaction->id."]\t... HTTP STATUS.."
                . $httpResponse->getStatusCode());
            Log::debug("[AppsMobile][dispatchAirtime][".$transaction->id."]\t... HTTP RESPONSE BODY.."
                .$httpResponse->getBody());

//            $httpResponse = json_decode($httpResponse->getBody(),true);

        } catch (ClientException $exception){
        Log::error("[AppsMobile][dispatchAirtime][".$transaction->id."]\t... ClientException..\t".
            $exception->getResponse()->getStatusCode());
        Log::error("[AppsMobile][dispatchAirtime][".$transaction->id."]\t... ClientException..\t".
            $exception->getResponse()->getBody());
        }catch (RequestException $exception){
            Log::error("[AppsMobile][dispatchAirtime][".$transaction->id."]\t... RequestException..\t".
                $exception->getResponse()->getStatusCode());
            Log::error("[AppsMobile][dispatchAirtime]\t... RequestException..\t".
                $exception->getResponse()->getBody());
        }catch (\Exception $exception){
            Log::error("[AppsMobile][dispatchAirtime][".$transaction->id."]\t... Exception..\t"
                .$exception->getMessage());
            Log::error("[AppsMobile][dispatchAirtime][".$transaction->id."]\t... Exception..\t"
                .$exception->getTraceAsString());
        }

    }

    public static function handleTransactionCallback(Transaction  $transaction,Request  $request){
        //TODO handle transaction callback
        Log::debug("[AppsMobile][handleTransactionCallback][".$transaction->id."]\t... callback body..".
            $request->getContent());


        if (strtolower(trim($transaction->status)) == "pending")
        {
            $statusMessage = $request->message;
            if (!empty($request->trans_status))
            {

                if (Str::startsWith($request->trans_status,"000/"))
                {
                    if (empty($statusMessage))
                    {
                        $statusMessage = "Processes Successfully";
                    }
                    \App\Utils\Transaction::updateTransactionStatus($transaction,"Success",$statusMessage);
                }else if (Str::startsWith($request->trans_status,"001/"))
                {
                    if (empty($statusMessage))
                    {
                        $statusMessage = "Failed At Partner Gateway";
                    }
                    \App\Utils\Transaction::updateTransactionStatus($transaction,"Error",$statusMessage);
                    //TODO reverse transaction
                }

                if (!empty($request->trans_id))
                {
                    $transaction->gateway_id = $request->trans_id;
                    $transaction->save();
                }
            }
        }else{
            Log::error("[AppsMobile][handleTransactionCallback][".$transaction->id."\t Transaction not in".
                " pending state.. exiting");

        }


    }
}