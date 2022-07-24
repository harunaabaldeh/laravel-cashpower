<?php


namespace App\Utils;


use App\Biller;
use App\Country;
use App\Transaction;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;



class Zeepay
{

    private static function getAccessToken(){
        try{
            $token = null;
            $startTime = Carbon::now();
            Log::debug("[Zeepay][getAccessToken]\t... Called..");

            $token =\Redis::get('x-cartis-pay-zeepay-token');
            if (empty($token)){
                $httpClient = new Client();
                $uri = env('ZEEPAY_GET_AUTH_TOKEN_END_POINT');

                Log::debug("[Zeepay][getAllowedAirtimePackages]\t... Calling URI..".$uri);


                $httpResponse = $httpClient->post($uri,[
                    'form_params' => [
                        'grant_type' => 'password',
                        'client_secret' => env('ZEEPAY_CLIENT_SECRET'),
                        'client_id' => env('ZEEPAY_CLIENT_ID'),
                        'username' => env('ZEEPAY_USERNAME'),
                        'password' => env('ZEEPAY_PASSWORD')
                    ]
                ]);

                Log::debug("[Zeepay][getAccessToken]\t... HTTP STATUS..".$httpResponse->getStatusCode());
                Log::debug("[Zeepay][getAccessToken]\t... HTTP RESPONSE BODY..".$httpResponse->getBody());

                $httpResponse = json_decode($httpResponse->getBody(),true);

                if (!empty($httpResponse) && is_array($httpResponse) && array_key_exists('access_token',
                        $httpResponse)){
                    $token = $httpResponse['access_token'];

                    \Redis::set('x-cartis-pay-zeepay-token',$token);
                    \Redis::expire('x-cartis-pay-zeepay-token',86400);
                }
            }

        }catch (ClientException $exception){
            Log::error("[Zeepay][getAccessToken]\t... ClientException..".
                $exception->getResponse()->getStatusCode());
            Log::error("[Zeepay][getAccessToken]\t... ClientException..".$exception->getResponse()->getBody());
        }catch (RequestException $exception){
            Log::error("[Zeepay][getAccessToken]\t... RequestException..".
                $exception->getResponse()->getStatusCode());
            Log::error("[Zeepay][getAccessToken]\t... RequestException..".
                $exception->getResponse()->getBody());
        }catch (\Exception $exception){
            Log::error("[Zeepay][getAccessToken]\t... Exception..".$exception->getMessage());
            Log::error("[Zeepay][getAccessToken]\t... Exception..".$exception->getTraceAsString());
        }

        $endTime = Carbon::now();

        Log::debug("[Zeepay][getAccessToken]\t... Time To Run..".($endTime->diffInSeconds($startTime)));
        return $token;
    }

    public static function getAllowedAirtimePackages(Country $country, $msisdn){
        try{

            Log::debug("[Zeepay][getAllowedAirtimePackages]\t... Called..MSISDN: ".$msisdn."\tCountry: ".
                $country->iso_3166_3);
            $startTime = Carbon::now();

            $responseBody = \Redis::hget('x-star-pay-airtime-packages',$msisdn);

            if (empty($responseBody))
            {
                $token = self::getAccessToken();

                if (!empty($token))
                {
                    Log::debug("[Zeepay][getAllowedAirtimePackages]\t... Called..");

                    $httpClient = new Client();
                    $uri = str_replace("@@countryCode@@",$country->iso_3166_3,str_replace(
                        "@@msisdn@@",$msisdn,env('ZEEPAY_GET_AIRTIME_PACKAGES_END_POINT')));

                    Log::debug("[Zeepay][getAllowedAirtimePackages]\t... Calling URI..".$uri);

                    $httpResponse = $httpClient->get($uri,[
                        'headers' => [
                            'Accept' => 'application/json',
                            'Authorization' => 'Bearer '.$token,
                        ]]);

                    Log::debug("[Zeepay][getAllowedAirtimePackages]\t... HTTP STATUS..".
                        $httpResponse->getStatusCode());
                    Log::debug("[Zeepay][getAllowedAirtimePackages]\t... HTTP RESPONSE BODY..".
                        $httpResponse->getBody());

                    $httpResponse = json_decode($httpResponse->getBody(),true);

                    $responseBody = array();
                    if (!empty($httpResponse) && is_array($httpResponse))
                    {
                        if (array_key_exists('allowedPackages',$httpResponse)){
                            $responseBody['packages'] = $httpResponse['allowedPackages'];
                        }

                        if (array_key_exists('mno',$httpResponse)){
                            $responseBody['mno'] = $httpResponse['mno'];
                        }

                        if (array_key_exists('currency',$httpResponse)){
                            $responseBody['currency'] = $httpResponse['currency'];
                        }

                        $responseBody['msisdn'] = $msisdn;

                    }

                    \Redis::hset('x-star-pay-airtime-packages',$msisdn,json_encode($responseBody));
                    return $responseBody;
                }else
                {
                    Log::error("[Zeepay][getAllowedAirtimePackages]\t... Token is empty.. Proceeding..");
                }
            }else{
                return json_decode($responseBody,true);
            }

        }catch (ClientException $exception){
            Log::error("[Zeepay][getAllowedAirtimePackages]\t... ClientException..".
                $exception->getResponse()->getStatusCode());
            Log::error("[Zeepay][getAllowedAirtimePackages]\t... ClientException..".
                $exception->getResponse()->getBody());
        }catch (RequestException $exception){
            Log::error("[Zeepay][getAllowedAirtimePackages]\t... RequestException..".
                $exception->getResponse()->getStatusCode());
            Log::error("[Zeepay][getAllowedAirtimePackages]\t... RequestException..".
                $exception->getResponse()->getBody());
        }catch (\Exception $exception){
            Log::error("[Zeepay][getAllowedAirtimePackages]\t... Exception..".
                $exception->getMessage());
            Log::error("[Zeepay][getAllowedAirtimePackages]\t... Exception..".$exception->getTraceAsString());
        }

        $endTime = Carbon::now();
        Log::debug("[Zeepay][getAllowedAirtimePackages]\t... Time To Run..".
            ($endTime->diffInSeconds($startTime)));
        return null;
    }

    public static function initiateTransaction(Transaction $transaction){

        Log::debug("[Zeepay][initiateTransaction][".$transaction->id."]\t...called for ... ",
            $transaction->toArray());
        try{

            $params = null;
            $startTime = now();
            $token = self::getAccessToken();

            if (empty($token))
            {
                Log::debug("[Zeepay][initiateTransaction][".$transaction->id."]\t... ".
                    "Empty Access Token... Exiting... ");
            }else
            {
                $senderFirstName = $transaction->user->firstname;
                $senderLastName = trim((!empty($transaction->user->othernames) ? $transaction->user->othernames :
                        "" )." ".$transaction->user->lastname);


                $uri = "";

                if (in_array($transaction->type,["Wallet","Bank","Pickup"]))
                {

                    $uri = env("ZEEPAY_PAYOUTS_END_POINT");
                    $requestBody = [
                        'sender_first_name' => $senderFirstName,
                        'sender_last_name' => $senderLastName,
                        'sender_country' => $transaction->user->country->iso_3166_3,
                        'receiver_first_name' => $transaction->beneficiary->firstname,
                        'receiver_last_name' => $transaction->beneficiary->lastname,
                        'receiver_country' => $transaction->beneficiary->country->iso_3166_3,
                        'sending_currency' => $transaction->user->country->currency_code,
                        'send_amount' => $transaction->source_amount,
                        'amount' => $transaction->destination_amount,
                        'receiver_currency' => $transaction->beneficiary->country->currency_code,
                        'extr_id' => $transaction->id,
                        'service_type' => $transaction->type,
                        'callback_url' => route('transactions.callback',[$transaction->id])
                    ];

                    if ($transaction->type == "Wallet" || $transaction->type == "Pickup"){
                        $requestBody['receiver_msisdn'] = $transaction->beneficiary->msisdn;
                    }

                    if ($transaction->type == "Bank"){
                        $requestBody['account_number'] = $transaction->beneficiary->account_number;
                        $requestBody['routing_number'] = $transaction->beneficiary->account_routing_number;
                    }

                    Log::debug("[Zeepay][initiateTransaction][".$transaction->id."]\t... HTTP Request Body.."
                        ,$requestBody);

                    $params = ['form_params' => $requestBody, 'headers' => ['Accept' => 'application/json',
                        'Authorization' => 'Bearer '.$token,]];

                }elseif ($transaction->type == "Airtime")
                {

                    $uri = env("ZEEPAY_AIRTIME_END_POINT");
                    $requestBody = [
                        'reference' => $transaction->id,
                        'packageAmount' => $transaction->destination_amount,
                        'receivingCountryCode' => $transaction->airtimeReceiverCountry,
                        'senderMsisdn' => $transaction->user->msisdn,
                        'receiverMsisdn' => $transaction->airtimeMsisdn,
                        'senderCountryCode' => $transaction->user->country->iso_3166_3,
                        'recipientName' => $transaction->airtimeReceiverName,
                        'senderName' => $senderFirstName." ".$senderLastName,
                        'service_type' => $transaction->type,
                        'callback_url' => route('zeepay.transactions.callback',[$transaction->id])
                    ];

                    Log::debug("[Zeepay][initiateTransaction][".$transaction->id."]\t... HTTP Request Body.."
                        ,$requestBody);

                    $params = ['json' => $requestBody, 'headers' => ['Accept' => 'application/json',
                        'Authorization' => 'Bearer '.$token,]];
                }

                if (!empty($params))
                {
                    Log::debug("[Zeepay][initiateTransaction][".$transaction->id."]\t... Request URL.." .$uri);
                    $httpClient = new Client();

                    $httpResponse = $httpClient->post($uri,$params);

                    Log::debug("[Zeepay][initiateTransaction][".$transaction->id."]\t... HTTP STATUS..".
                        $httpResponse->getStatusCode());
                    Log::debug("[Zeepay][initiateTransaction][".$transaction->id."]\t... HTTP RESPONSE BODY..".
                        $httpResponse->getBody());

                    $httpResponse = json_decode($httpResponse->getBody(),true);

                    if (!empty($httpResponse) && is_array($httpResponse) && array_key_exists('zeepay_id',
                            $httpResponse)){
                        $transaction->gateway_id = $httpResponse['zeepay_id'];
                        $transaction->status_message =  "Transaction in Progress";

                        $transaction->save();
                    }
                }else
                {
                    Log::error("[Zeepay][initiateTransaction][".$transaction->id."]\t... ".
                        "Request Body Empty.. exiting ..");

                }


            }


        }catch (ClientException $exception){
            Log::error("[Zeepay][initiateTransaction][".$transaction->id."]\t... ClientException..".
                $exception->getResponse()->getStatusCode());
            Log::error("[Zeepay][initiateTransaction][".$transaction->id."]\t... ClientException..".
                $exception->getResponse()->getBody());
        }catch (RequestException $exception){
            Log::error("[Zeepay][initiateTransaction][".$transaction->id."]\t... RequestException..".
                $exception->getResponse()->getStatusCode());
            Log::error("[Zeepay][initiateTransaction][".$transaction->id."]\t... RequestException..".
                $exception->getResponse()->getBody());
        }catch (\Exception $exception){
            Log::error("[Zeepay][initiateTransaction][".$transaction->id."]\t... Exception..".
                $exception->getMessage());
            Log::error("[Zeepay][initiateTransaction][".$transaction->id."]\t... Exception..".
                $exception->getTraceAsString());
        }

        $endTime = Carbon::now();
        Log::debug("[Zeepay][initiateTransaction][".$transaction->id."]\t... Time To Run..".
            ($endTime->diffInSeconds($startTime)));
    }

    public static function handleBillPaymentValidation(Biller  $biller, $payload){

        $startTime = now();
        Log::debug("[Zeepay][handleBillPaymentValidation]\t...running validation for ... ",
            $biller->toArray());

        Log::debug("[Zeepay][handleBillPaymentValidation]\t...payload... ",
            $payload);

        try{
            $token = self::getAccessToken();

            if (empty($token))
            {
                Log::debug("[Zeepay][handleBillPaymentValidation]\t... Empty Access Token... Exiting... ");
            }else
            {

                $uri = str_replace("@@biller_id@@",$biller->zeepayId,
                    env("ZEEPAY_BILLERS_VALIDATION_END_POINT"));

                $params = ['json' => $payload, 'headers' => ['Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$token,]];

                if (!empty($params))
                {
                    Log::debug("[Zeepay][handleBillPaymentValidation]\t... Request URL.." .$uri);
                    $httpClient = new Client();

                    $httpResponse = $httpClient->post($uri,$params);

                    Log::debug("[Zeepay][handleBillPaymentValidation]\t... HTTP STATUS..".
                        $httpResponse->getStatusCode());
                    Log::debug("[Zeepay][handleBillPaymentValidation]\t... HTTP RESPONSE BODY..".
                        $httpResponse->getBody());

                    return json_decode($httpResponse->getBody(),true);
                }else
                {
                    Log::error("[Zeepay][handleBillPaymentValidation]\t... ".
                        "Request Body Empty.. exiting ..");

                }


            }


        }catch (ClientException $exception){
            Log::error("[Zeepay][handleBillPaymentValidation]\t... ClientException..".
                $exception->getResponse()->getStatusCode());
            Log::error("[Zeepay][handleBillPaymentValidation]\t... ClientException..".
                $exception->getResponse()->getBody());
        }catch (RequestException $exception){
            Log::error("[Zeepay][handleBillPaymentValidation]\t... RequestException..".
                $exception->getResponse()->getStatusCode());
            Log::error("[Zeepay][handleBillPaymentValidation]\t... RequestException..".
                $exception->getResponse()->getBody());
        }catch (\Exception $exception){
            Log::error("[Zeepay][handleBillPaymentValidation]\t... Exception..".
                $exception->getMessage());
            Log::error("[Zeepay][handleBillPaymentValidation]\t... Exception..".
                $exception->getTraceAsString());
        }

        $endTime = Carbon::now();
        Log::debug("[Zeepay][handleBillPaymentValidation]\t... Time To Run..".
            ($endTime->diffInSeconds($startTime)));
        return null;
    }

    public static function completeBillPayment(Biller  $biller, $reference, $payload){

        $startTime = now();
        Log::debug("[Zeepay][completeBillPayment]\t...running validation for ... ",
            $biller->toArray());

        Log::debug("[Zeepay][completeBillPayment]\t...payload... ",
            $payload);

        try{
            $token = self::getAccessToken();

            if (empty($token))
            {
                Log::debug("[Zeepay][completeBillPayment]\t... Empty Access Token... Exiting... ");
            }else
            {

                $zeepay_id = \Redis::get($reference);

                $uri = str_replace("@@zeepay_id@@",$zeepay_id,
                    env("ZEEPAY_BILLERS_MAKE_PAYMENT_END_POINT"));

                $params = ['json' => $payload, 'headers' => ['Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$token,]];

                if (!empty($params))
                {
                    Log::debug("[Zeepay][completeBillPayment]\t... Request URL.." .$uri);
                    $httpClient = new Client();

                    $httpResponse = $httpClient->post($uri,$params);

                    Log::debug("[Zeepay][completeBillPayment]\t... HTTP STATUS..".
                        $httpResponse->getStatusCode());
                    Log::debug("[Zeepay][completeBillPayment]\t... HTTP RESPONSE BODY..".
                        $httpResponse->getBody());

                    //TODO handle status check
//                    $zeepayResponse = json_decode($httpResponse->getBody(),true);
                }else
                {
                    Log::error("[Zeepay][completeBillPayment]\t... ".
                        "Request Body Empty.. exiting ..");

                }


            }


        }catch (ClientException $exception){
            Log::error("[Zeepay][completeBillPayment]\t... ClientException..".
                $exception->getResponse()->getStatusCode());
            Log::error("[Zeepay][completeBillPayment]\t... ClientException..".
                $exception->getResponse()->getBody());
        }catch (RequestException $exception){
            Log::error("[Zeepay][completeBillPayment]\t... RequestException..".
                $exception->getResponse()->getStatusCode());
            Log::error("[Zeepay][completeBillPayment]\t... RequestException..".
                $exception->getResponse()->getBody());
        }catch (\Exception $exception){
            Log::error("[Zeepay][completeBillPayment]\t... Exception..".
                $exception->getMessage());
            Log::error("[Zeepay][completeBillPayment]\t... Exception..".
                $exception->getTraceAsString());
        }

        $endTime = Carbon::now();
        Log::debug("[Zeepay][completeBillPayment]\t... Time To Run..".
            ($endTime->diffInSeconds($startTime)));
        return null;
    }

    public static function handleTransactionCallBack(Transaction $transaction, Request $request){
        $httpResponse = json_decode($request->getContent(),true);

        if (!empty($httpResponse) && is_array($httpResponse) && array_key_exists('code',$httpResponse)){

            if ($httpResponse['code']== 200){
                $transaction->status = "Success";
                $transaction->status_message = array_key_exists('message',$httpResponse) ? $httpResponse['message']
                    : "Transaction Processed Successfully";
                $transaction->save();
            }

            if ($httpResponse['code']== 400){
                $transaction->status = "Error";
                $transaction->status_message = array_key_exists('message',$httpResponse) ? $httpResponse['message']
                    : "Transaction Failed";
                $transaction->save();
            }


        }
    }


}