<?php


namespace App\Utils;



use App\Beneficiary;
use App\Http\Controllers\UtilitiesController;
use App\Transaction;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class GamSwitch
{


    private static function getAccessToken(){

        Log::info("[GamSwitch][getAccessToken] \t... getting access token");
        try
        {

            if (Redis::exists('accessTokenCache'))
            {
                Log::info("[GamSwitch][getAccessToken] \t... returning cached access tokens");
                return Redis::get('accessTokenCache');
            }



            $httpClient = new Client();
            $uri = env("GAM_SWITCH_TOKEN_END_POINT");

            Log::info("[GamSwitch][getAccessToken] \t... Request URI\t".$uri);
            $httpResponse = $httpClient->post($uri,[
                'form_params' =>[
                    'grant_type' => 'password',
                    'Username' => env('GAM_SWITCH_USERNAME'),
                    'Password' => env('GAM_SWITCH_PASSWORD'),
                ],
                'verify' => false
            ]);

            Log::info("[GamSwitch][getAccessToken] \t... HTTP Status Code: ".$httpResponse->getStatusCode());
            Log::info("[GamSwitch][getAccessToken] \t... HTTP Response Body:  ".$httpResponse->getBody());

            $httpResponseBody = json_decode($httpResponse->getBody(),true);

            if (!empty($httpResponseBody) && is_array($httpResponseBody) &&
                array_key_exists('access_token',$httpResponseBody))
            {
                Redis::set('accessTokenCache',$httpResponseBody['access_token']);
                Redis::expire('accessTokenCache',86400);
                return $httpResponseBody['access_token'];
            }


        }catch (ServerException $exception){
            Log::info("[GamSwitch][getAccessToken] \t... ServerException getting access token: "
                .$exception->getResponse()->getBody()->getContents());
        } catch (ClientException $exception){
            Log::info("[GamSwitch][getAccessToken] \t... ClientException getting access token: "
                .$exception->getResponse()->getBody()->getContents());
        } catch (TransferException $exception){
            Log::info("[GamSwitch][getAccessToken] \t... TransferException getting access token: "
                .$exception->getMessage());
        }catch (\Exception $exception){
            Log::info("[GamSwitch][getAccessToken] \t... Exception getting access token: "
                .$exception->getMessage());
            Log::info("[GamSwitch][getAccessToken] \t ".$exception->getTraceAsString());
        }

        return null;
    }


    public static function validateMeterNumber($amount,$meterNumber,$phoneNumber){

        $session = Uuid::uuid4()->toString();
        Log::info("[GamSwitch][validateMeterNumber][".$session."]\t Called for Amount: ".$amount
            .",\tMeter Number: ".$meterNumber.",\t Phone Number: ".$phoneNumber);
        try{
            $accessToken = self::getAccessToken();

            if (empty($accessToken)){
                return array("failed","Temporal System Failure",null);
            }


            $nounce = time().rand(10000,99999);
            $requestTimestamp = time();

            Log::info("[GamSwitch][validateMeterNumber][".$session."]\t Nounce\t".$nounce."\t Timestamp: "
                .$requestTimestamp);

            $signature = env('GAM_SWITCH_HASH_KEY')."nawec"."consumercheck".$nounce. $requestTimestamp
                .$meterNumber;

            $httpClient = new Client();
            Log::info("[GamSwitch][validateMeterNumber][".$session."]\t Signature Pre-Hash\t".$signature);

            $signature = hash("sha512", $signature);

            Log::info("[GamSwitch][validateMeterNumber][".$session."]\t Signature Post-Hash\t".$signature);

            $requestBody = ["Amount" => $amount, "Type" => "Consumercheck", "MeterNumber" => $meterNumber,
                "PhoneNumber" => $phoneNumber,];

            Log::info("[GamSwitch][validateMeterNumber][".$session."]\t Request Body \t",$requestBody);

            $uri = env('GAM_SWITCH_METER_VALIDATION_END_POINT');
            $requestHeader = ["Nonce" => $nounce, "Timestamp" => $requestTimestamp, "Authorization" =>
                'Bearer ' . $accessToken, "Signature" => $signature];

            Log::info("[GamSwitch][validateMeterNumber][".$session."]\t Request Header \t",$requestHeader);
            Log::info("[GamSwitch][validateMeterNumber][".$session."]\t Request URI \t".$uri);

            $httpResponse = $httpClient->post($uri,[
                'headers' => $requestHeader,
                'json' => $requestBody,
                'verify' => false
            ]);

            Log::info("[GamSwitch][validateMeterNumber][".$session."]\t Response Status Code \t"
                .$httpResponse->getStatusCode());
            Log::info("[GamSwitch][validateMeterNumber][".$session."]\t Response Body \t"
                .$httpResponse->getBody());

            $httpResponse = json_decode($httpResponse->getBody(),true);

            if (!empty($httpResponse) && is_array($httpResponse) && array_key_exists('responseCode',$httpResponse))
            {

                if ( $httpResponse['responseCode']  != "0")
                {
                    return array("failed","Invalid/Unregistered Meter Number",null);
                }

                if (array_key_exists('nawec',$httpResponse) && !empty($httpResponse['nawec']) && array_key_exists(
                    'CustomerName',$httpResponse['nawec']) && !empty($httpResponse['nawec']['CustomerName']) ) {
                    $meterOwnersName = $httpResponse['nawec']['CustomerName'];
                    return  array("success","validation successful",$meterOwnersName);
                }

            }else
            {
                return array("failed","Sorry something went wrong. please try again later.",null);
            }

        }catch (ServerException $exception){
            Log::info("[GamSwitch][validateMeterNumber][".$session."] \tServerException getting access token: "
                .$exception->getResponse()->getBody()->getContents());
        } catch (ClientException $exception){
            Log::info("[GamSwitch][validateMeterNumber][".$session."] \tClientException getting access token: "
                .$exception->getResponse()->getBody()->getContents());
        } catch (TransferException $exception){
            Log::info("[GamSwitch][validateMeterNumber][".$session."] \tTransferException getting access "
                ."token: ".$exception->getMessage());
        }catch (\Exception $exception){
            Log::info("[GamSwitch][validateMeterNumber][".$session."] \t... Exception getting access token: "
                .$exception->getMessage());
            Log::info("[GamSwitch][validateMeterNumber][".$session."] \t ".$exception->getTraceAsString());
        }


        return array("failed","unknown error",null);
    }


/*    public static function accountNameCheck($accountNumber){

        $session = Uuid::uuid4()->toString();
        Log::info("[GamSwitch][accountNameCheck][".$session."]\t Called for Account Number: ".$accountNumber);
        try{
            $accessToken = self::getAccessToken();

            if (empty($accessToken)){
                return array("failed","Temporal System Failure",null);
            }


            $nounce = Str::random(10);
            $requestTimestamp = time();
            $signature = env('GAM_SWITCH_HASH_KEY')."namecheck".$nounce. $requestTimestamp .$accountNumber;

            $httpClient = new Client();
            Log::info("[GamSwitch][accountNameCheck][".$session."]\t Signature Pre-Hash\t".$signature);

            $signature = hash("sha512", $signature);

            Log::info("[GamSwitch][accountNameCheck][".$session."]\t Signature Post-Hash\t".$signature);

            $requestBody = ["Amount" => $amount, "Type" => "Consumercheck", "MeterNumber" => $meterNumber,
                "PhoneNumber" => $phoneNumber,];

            Log::info("[GamSwitch][accountNameCheck][".$session."]\t Request Body \t",$requestBody);

            $uri = env('GAM_SWITCH_METER_VALIDATION_END_POINT');
            $requestHeader = ["Nonce" => $nounce, "Timestamp" => $requestTimestamp, "Authorization" =>
                'Bearer ' . $accessToken, "Signature" => $signature];

            Log::info("[GamSwitch][accountNameCheck][".$session."]\t Request Header \t",$requestHeader);
            Log::info("[GamSwitch][accountNameCheck][".$session."]\t Request URI \t".$uri);

            $httpResponse = $httpClient->post($uri,[
                'headers' => $requestHeader,
                'json' => $requestBody,
                'verify' => false
            ]);

            Log::info("[GamSwitch][accountNameCheck][".$session."]\t Response Status Code \t"
                .$httpResponse->getStatusCode());
            Log::info("[GamSwitch][accountNameCheck][".$session."]\t Response Body \t"
                .$httpResponse->getBody());

            if (!empty($httpResponse) && is_array($httpResponse) && array_key_exists('responseCode',$httpResponse))
            {

                if ( $httpResponse['responseCode']  != "0")
                {
                    return array("failed","Invalid/Unregistered Meter Number",null);
                }

                if (array_key_exists('nawec',$httpResponse) && !empty($httpResponse['nawec']) && array_key_exists(
                    'CustomerName',$httpResponse['nawec']) && !empty($httpResponse['nawec']['CustomerName']) ) {
                    $meterOwnersName = $httpResponse['nawec']['CustomerName'];
                    return  array("success","validation successful",$meterOwnersName);
                }

            }else
            {
                return array("failed","Sorry something went wrong. please try again later.",null);
            }

        }catch (ServerException $exception){
            Log::info("[GamSwitch][accountNameCheck][".$session."] \tServerException getting access token: "
                .$exception->getResponse()->getBody()->getContents());
        } catch (ClientException $exception){
            Log::info("[GamSwitch][accountNameCheck][".$session."] \tClientException getting access token: "
                .$exception->getResponse()->getBody()->getContents());
        } catch (TransferException $exception){
            Log::info("[GamSwitch][accountNameCheck][".$session."] \tTransferException getting access token: "
                .$exception->getMessage());
        }catch (\Exception $exception){
            Log::info("[GamSwitch][accountNameCheck][".$session."] \t... Exception getting access token: "
                .$exception->getMessage());
            Log::info("[GamSwitch][accountNameCheck][".$session."] \t ".$exception->getTraceAsString());
        }


        return array("failed","unknown error",null);
    }
*/

    public static function checkTransactionStatus(Transaction $transaction){

        $session = Uuid::uuid4()->toString();
        Log::info("[GamSwitch][checkTransactionStatus][".$session."][".$transaction->id."]\t ");
        try{


            $accessToken = self::getAccessToken();

            if (empty($accessToken)){
                return null;
            }


            $nounce = time().rand(10000,99999);
            $requestTimestamp = time();
            $signature = env('GAM_SWITCH_HASH_KEY')."query".$nounce. $requestTimestamp
                .$transaction->api_reference;

            $httpClient = new Client();
            Log::info("[GamSwitch][checkTransactionStatus][".$session."][".$transaction->id."]\t".
                " Signature Pre-Hash\t".$signature);

            $signature = hash("sha512", $signature);

            Log::info("[GamSwitch][checkTransactionStatus][".$session."][".$transaction->id."]\t "
                ."Signature Post-Hash\t".$signature);

            $requestHeader = ["Nonce" => $nounce, "Timestamp" => $requestTimestamp, "Authorization" =>
                'Bearer ' . $accessToken, "Signature" => $signature];

            Log::info("[GamSwitch][checkTransactionStatus][".$session."][".$transaction->id."]\t "
                ."Request Header \t",$requestHeader);
            $uri = str_replace("{id}",$transaction->api_reference,
                env('GAM_SWITCH_CHECK_TRANSACTION_STATUS_END_POINT'));

            Log::info("[GamSwitch][checkTransactionStatus][".$session."][".$transaction->id."]\t"
                ." Request URI \t".$uri);
            $httpResponse = $httpClient->post($uri,[
                'headers' => $requestHeader,
                'verify' => false
            ]);

            Log::info("[GamSwitch][checkTransactionStatus][".$session."][".$transaction->id."]\t" .
                " Response Status Code \t"
                .$httpResponse->getStatusCode());
            Log::info("[GamSwitch][checkTransactionStatus][".$session."][".$transaction->id."]\t".
                " Response Body \t" .$httpResponse->getBody());

            if (!empty($httpResponse) && is_array($httpResponse) && array_key_exists('responseCode',$httpResponse))
            {

                if ( $httpResponse['responseCode']  == "0")
                {
//                    UtilitiesController::debitUserWallet($transaction->user,$transaction);
                    \App\Utils\Transaction::updateTransactionStatus($transaction, "Success",
                        "Transaction Processed Successfully");

                }

            }
        }catch (ServerException $exception){
            Log::info("[GamSwitch][checkTransactionStatus][".$session."][".$transaction->id."] \t... 
            ServerException getting access token: ".$exception->getResponse()->getBody()->getContents());
        } catch (ClientException $exception){
            Log::info("[GamSwitch][checkTransactionStatus][".$session."][".$transaction->id."] \t... 
            ClientException getting access token: ".$exception->getResponse()->getBody()->getContents());
        } catch (TransferException $exception){
            Log::info("[GamSwitch][checkTransactionStatus][".$session."][".$transaction->id."] \t... 
            TransferException getting access token: ".$exception->getMessage());
        }catch (\Exception $exception){
            Log::info("[GamSwitch][checkTransactionStatus][".$session."][".$transaction->id."] \t... 
            Exception getting access token: ".$exception->getMessage());
            Log::info("[GamSwitch][checkTransactionStatus][".$session."][".$transaction->id."] \t "
                .$exception->getTraceAsString());
        }
    }


    public static function doElectricityTopUp(Transaction $transaction, Beneficiary  $beneficiary){

        $session = Uuid::uuid4()->toString();
        Log::info("[GamSwitch][doElectricityTopUp][".$session."][".$transaction->id."]\t ");
        try{

            $accessToken = self::getAccessToken();

            if (empty($accessToken)){
                return null;
            }

            $accountDetails = explode("_",trim($beneficiary->account_number));

            $meterNumber =  $accountDetails[0];
            $phoneNumber =  $accountDetails[1];

            $nounce = time().rand(10000,99999);
            $requestTimestamp = time();
            $signature = env('GAM_SWITCH_HASH_KEY')."nawec"."vend".$nounce. $requestTimestamp .$meterNumber;

            $httpClient = new Client();
            Log::info("[GamSwitch][doElectricityTopUp][".$session."][".$transaction->id."]\t Signature"
                ." Pre-Hash\t".$signature);

            $signature = hash("sha512", $signature);

            Log::info("[GamSwitch][doElectricityTopUp][".$session."][".$transaction->id."]\t Signature "
                ."Post-Hash\t".$signature);

            $requestHeader = ["Nonce" => $nounce, "Timestamp" => $requestTimestamp, "Authorization" =>
                'Bearer ' . $accessToken, "Signature" => $signature];

            $requestBody = ["Type" => "Vend","MeterNumber" => $meterNumber
                ,"PhoneNumber" => $phoneNumber,
                "Amount" => $transaction->destination_amount * 100,"AccountType" => "Default",
                "AccountNumber" => env("GAM_SWITCH_CARD_NUMBER")];
            Log::info("[GamSwitch][doElectricityTopUp][".$session."][".$transaction->id."]\t"
                ." Request Header \t",$requestHeader);
            Log::info("[GamSwitch][doElectricityTopUp][".$session."][".$transaction->id."]\t"
                ." Request Body \t",$requestBody);

            $uri = env('GAM_SWITCH_PREPAID_VEND_END_POINT');

            Log::info("[GamSwitch][doElectricityTopUp][".$session."][".$transaction->id."]\t"
                ." Request URI \t".$uri);

            $beneficiary->bank_name = "Cash Power";
            $beneficiary->save();
            $transaction->api_reference = $nounce;
            $transaction->save();

            $httpResponse = $httpClient->post($uri,[
                'headers' => $requestHeader,
                'verify' => false,
                "json" => $requestBody
            ]);

            Log::info("[GamSwitch][doElectricityTopUp][".$session."][".$transaction->id."]\t"
                ." Response Status Code \t"
                .$httpResponse->getStatusCode());
            Log::info("[GamSwitch][doElectricityTopUp][".$session."][".$transaction->id."]\t Response Body \t"
                .$httpResponse->getBody());

            $httpResponse = json_decode($httpResponse->getBody(),true);
            if (!empty($httpResponse) && is_array($httpResponse) && array_key_exists('responseCode',$httpResponse))
            {

                if ( $httpResponse['responseCode']  == "0")
                {
                    dispatch(new \App\Jobs\FetchCashPowerToken($transaction));
//                    UtilitiesController::debitUserWallet($transaction->user,$transaction);
                    if (empty($statusMessage))
                    {
                        $statusMessage = "Processes Successfully";
                    }
                    \App\Utils\Transaction::updateTransactionStatus($transaction,"Success",$statusMessage);
                }

            }
        }catch (ServerException $exception){
            Log::info("[GamSwitch][doElectricityTopUp][".$session."][".$transaction->id."] \t... 
            ServerException getting access token: ".$exception->getResponse()->getBody()->getContents());
        } catch (ClientException $exception){
            Log::info("[GamSwitch][doElectricityTopUp][".$session."][".$transaction->id."] \t... 
            ClientException getting access token: ".$exception->getResponse()->getBody()->getContents());
        } catch (TransferException $exception){
            Log::info("[GamSwitch][doElectricityTopUp][".$session."][".$transaction->id."] \t... 
            TransferException getting access token: ".$exception->getMessage());
        }catch (\Exception $exception){
            Log::info("[GamSwitch][doElectricityTopUp][".$session."][".$transaction->id."] \t... 
            Exception during Vend Operation: ".$exception->getMessage());
            Log::info("[GamSwitch][doElectricityTopUp][".$session."][".$transaction->id."] \t "
                .$exception->getTraceAsString());
        }
    }


    public static function getCashPowerToken(Transaction $transaction){

        $session = Uuid::uuid4()->toString();
        Log::info("[GamSwitch][getCashPowerToken][".$session."][".$transaction->id."]\t ");
        try{

            $accessToken = self::getAccessToken();

            if (empty($accessToken)){
                return null;
            }

            $beneficiary = $transaction->beneficiary;

            $accountDetails = explode("_",trim($beneficiary->account_number));

            $meterNumber =  $accountDetails[0];
            $phoneNumber =  $accountDetails[1];

            $nounce = time().rand(10000,99999);
            $requestTimestamp = time();
            $signature = env('GAM_SWITCH_HASH_KEY')."nawec"."reprint".$nounce. $requestTimestamp .$meterNumber;

            $httpClient = new Client();
            Log::info("[GamSwitch][getCashPowerToken][".$session."][".$transaction->id."]\t Signature"
                ." Pre-Hash\t".$signature);

            $signature = hash("sha512", $signature);

            Log::info("[GamSwitch][getCashPowerToken][".$session."][".$transaction->id."]\t Signature "
                ."Post-Hash\t".$signature);

            $requestHeader = ["Nonce" => $nounce, "Timestamp" => $requestTimestamp, "Authorization" =>
                'Bearer ' . $accessToken, "Signature" => $signature];

            $requestBody = ["Type" => "Reprint","MeterNumber" => $meterNumber,"PhoneNumber" => $phoneNumber];

            Log::info("[GamSwitch][getCashPowerToken][".$session."][".$transaction->id."]\t"
                ." Request Header \t",$requestHeader);
            Log::info("[GamSwitch][getCashPowerToken][".$session."][".$transaction->id."]\t"
                ." Request Body \t",$requestBody);

            $uri = env('GAM_SWITCH_PREPAID_VEND_END_POINT');

            Log::info("[GamSwitch][getCashPowerToken][".$session."][".$transaction->id."]\t"
                ." Request URI \t".$uri);

            $transaction->api_reference = $nounce;
            $transaction->save();

            $httpResponse = $httpClient->post($uri,[
                'headers' => $requestHeader,
                'verify' => false,
                "json" => $requestBody
            ]);

            Log::info("[GamSwitch][getCashPowerToken][".$session."][".$transaction->id."]\t"
                ." Response Status Code \t"
                .$httpResponse->getStatusCode());
            Log::info("[GamSwitch][getCashPowerToken][".$session."][".$transaction->id."]\t Response Body \t"
                .$httpResponse->getBody());

            $httpResponse = json_decode($httpResponse->getBody(),true);
            if (!empty($httpResponse) && is_array($httpResponse) && array_key_exists('responseCode',$httpResponse))
            {

                if ( $httpResponse['responseCode']  == "0")
                {
                    if (array_key_exists('nawec',$httpResponse)
                        && array_key_exists('Tokens',$httpResponse['nawec']))
                    {
                        $transaction->partner_receipt_reference = $httpResponse['nawec']['Tokens'];
                        $transaction->save();
                    }
                }

            }
        }catch (ServerException $exception){
            Log::info("[GamSwitch][getCashPowerToken][".$session."][".$transaction->id."] \t... 
            ServerException getting access token: ".$exception->getResponse()->getBody()->getContents());
        } catch (ClientException $exception){
            Log::info("[GamSwitch][getCashPowerToken][".$session."][".$transaction->id."] \t... 
            ClientException getting access token: ".$exception->getResponse()->getBody()->getContents());
        } catch (TransferException $exception){
            Log::info("[GamSwitch][getCashPowerToken][".$session."][".$transaction->id."] \t... 
            TransferException getting access token: ".$exception->getMessage());
        }catch (\Exception $exception){
            Log::info("[GamSwitch][getCashPowerToken][".$session."][".$transaction->id."] \t... 
            Exception during Vend Operation: ".$exception->getMessage());
            Log::info("[GamSwitch][getCashPowerToken][".$session."][".$transaction->id."] \t "
                .$exception->getTraceAsString());
        }
    }


    public static function doAirtimeTopUp(Transaction $transaction){

        $session = Uuid::uuid4()->toString();
        Log::info("[GamSwitch][doAirtimeTopUp][".$session."][".$transaction->id."]\t ");
        try{

            $accessToken = self::getAccessToken();

            if (empty($accessToken)){
                return null;
            }

            $airtimeMSISDN = $transaction->beneficiary->msisdn;

            if (Str::startsWith($airtimeMSISDN,"+")){
                $airtimeMSISDN = substr($airtimeMSISDN,1);
            }

            $nounce = time().rand(10000,99999);
            $requestTimestamp = time();

            $destinationCountry = \Countries::where('iso_3166_3',$transaction->airtimeReceiverCountry)->first();

            if (empty($destinationCountry)){
                Log::info("[GamSwitch][doAirtimeTopUp][".$session."][".$transaction->id."]\t".
                    " Can't resolve transaction destination. Trying with Currency");

                $destinationCountry = \Countries::where('currency_code',$transaction->destination_currency)->first();
                if (empty($destinationCountry)){
                    //TODO fail  transaction
                    return ;
                }
            }

            $type = strtolower(trim(UtilitiesController::getMNOFromMSISDN($airtimeMSISDN,
                $destinationCountry->iso_3166_2)));

            $transaction->beneficiary->bank_name = $type;
            $transaction->beneficiary->save();
            $signature = env('GAM_SWITCH_HASH_KEY')."airtime".$type.$nounce. $requestTimestamp .
                $airtimeMSISDN;

            $httpClient = new Client();
            Log::info("[GamSwitch][doAirtimeTopUp][".$session."][".$transaction->id."]\t Signature Pre-Hash\t"
                .$signature);

            $signature = hash("sha512", $signature);

            Log::info("[GamSwitch][doAirtimeTopUp][".$session."][".$transaction->id."]\t Signature Post-Hash\t"
                .$signature);

            $requestHeader = ["Nonce" => $nounce, "Timestamp" => $requestTimestamp, "Authorization" =>
                'Bearer ' . $accessToken, "Signature" => $signature];

            $requestBody = ["Type" => $type,"PhoneNumber" => $airtimeMSISDN,
                "Amount" => intval($transaction->destination_amount * 100),"AccountType" => "Default",
                "AccountNumber" => env("GAM_SWITCH_ACCOUNT_NUMBER")];

            Log::info("[GamSwitch][doAirtimeTopUp][".$session."][".$transaction->id."]\t Request Header \t"
                ,$requestHeader);
            Log::info("[GamSwitch][doAirtimeTopUp][".$session."][".$transaction->id."]\t Request Body \t"
                ,$requestBody);

            $uri = env('GAM_SWITCH_AIRTIME_VEND_END_POINT');

            Log::info("[GamSwitch][doAirtimeTopUp][".$session."][".$transaction->id."]\t Request URI \t".$uri);

            $transaction->api_reference = $nounce;
            $transaction->save();

            $httpResponse = $httpClient->post($uri,[
                'headers' => $requestHeader,
                'verify' => false,
                "json" => $requestBody
            ]);

            Log::info("[GamSwitch][doAirtimeTopUp][".$session."][".$transaction->id."]\t "
                ."Response Status Code \t" .$httpResponse->getStatusCode());
            Log::info("[GamSwitch][doAirtimeTopUp][".$session."][".$transaction->id."]\t Response Body \t"
                .$httpResponse->getBody());

            $httpResponse = json_decode($httpResponse->getBody(),true);
            if (!empty($httpResponse) && is_array($httpResponse) && array_key_exists('responseCode',$httpResponse))
            {

                if ( $httpResponse['responseCode']  == "0")
                {
//                    UtilitiesController::debitUserWallet($transaction->user,$transaction);
                    \App\Utils\Transaction::updateTransactionStatus($transaction, "Success",
                        "Transaction Processed Successfully");

                }else{
                    \App\Utils\Transaction::updateTransactionStatus($transaction, "Error",
                        "Failed At Gateway");

                }

            }else{
                \App\Utils\Transaction::updateTransactionStatus($transaction, "Error",
                    "General Failure");
            }
        }catch (ServerException $exception){
            Log::info("[GamSwitch][doAirtimeTopUp][".$session."][".$transaction->id."] \t... 
            ServerException getting access token: ".$exception->getResponse()->getBody()->getContents());
        } catch (ClientException $exception){
            Log::info("[GamSwitch][doAirtimeTopUp][".$session."][".$transaction->id."] \t... 
            ClientException getting access token: ".$exception->getResponse()->getBody()->getContents());
        } catch (TransferException $exception){
            Log::info("[GamSwitch][doAirtimeTopUp][".$session."][".$transaction->id."] \t... 
            TransferException getting access token: ".$exception->getMessage());
        }catch (\Exception $exception){
            Log::info("[GamSwitch][doAirtimeTopUp][".$session."][".$transaction->id."] \t... 
            Exception getting access token: ".$exception->getMessage());
            Log::info("[GamSwitch][doAirtimeTopUp][".$session."][".$transaction->id."] \t "
                .$exception->getTraceAsString());
        }
    }


    public static function getOVABalance(){

        $session = Uuid::uuid4()->toString();
        Log::info("[GamSwitch][getOVABalance][".$session."]\t ");
        try{

            $accessToken = self::getAccessToken();

            if (empty($accessToken)){
                return null;
            }

            $nounce = time().rand(10000,99999);
            $requestTimestamp = time();
            $signature = env('GAM_SWITCH_HASH_KEY')."balance".$nounce. $requestTimestamp;

            $httpClient = new Client();
            Log::info("[GamSwitch][getOVABalance][".$session."]\t Key\t".env('GAM_SWITCH_HASH_KEY'));
            Log::info("[GamSwitch][getOVABalance][".$session."]\t nounce\t".$nounce);
            Log::info("[GamSwitch][getOVABalance][".$session."]\t timestamp\t".$requestTimestamp);
            Log::info("[GamSwitch][getOVABalance][".$session."]\t Card Number\t".env('GAM_SWITCH_CARD_NUMBER'));

            Log::info("[GamSwitch][getOVABalance][".$session."]\t Signature Pre-Hash\t".$signature);

            $signature = hash("sha512", $signature);

            Log::info("[GamSwitch][getOVABalance][".$session."]\t Signature Post-Hash\t".$signature);

            $requestHeader = ["Nonce" => $nounce, "Timestamp" => $requestTimestamp, "Authorization" => 'Bearer '
                . $accessToken, "Signature" => $signature];

            Log::info("[GamSwitch][getOVABalance][".$session."]\t Request Header \t",$requestHeader);

            $uri = env("GAM_SWITCH_GET_OVA_BALANCE_END_POINT");
            $requestPayload = ['Amount' => "0", 'AccountType' => 'Current'];
            Log::info("[GamSwitch][getOVABalance][".$session."]\t Request URI \t".$uri);
            Log::info("[GamSwitch][getOVABalance][".$session."]\t Request Payload \t",$requestPayload);

            $httpResponse = $httpClient->post($uri,[
                'headers' => $requestHeader,
                'verify' => false,
                "json" => $requestPayload
            ]);

            Log::info("[GamSwitch][getOVABalance][".$session."]\t Response Status Code \t"
                .$httpResponse->getStatusCode());
            $responseBody = $httpResponse->getBody();
            Log::info("[GamSwitch][getOVABalance][".$session."]\t Response Body \t" . trim($responseBody));

            $httpResponse = json_decode($responseBody,true);

            if (!empty($httpResponse) && is_array($httpResponse) && array_key_exists('responseCode',$httpResponse))
            {

                if ( $httpResponse['responseCode']  == "0")
                {
                    //TODO send balance info via email

                    return  $httpResponse;
                }

            }
        }catch (ServerException $exception){
            Log::info("[GamSwitch][getOVABalance][".$session."] \t... 
            ServerException getting access token: ".$exception->getResponse()->getBody()->getContents());
        } catch (ClientException $exception){
            Log::info("[GamSwitch][getOVABalance][".$session."] \t... 
            ClientException getting access token: ".$exception->getResponse()->getBody()->getContents());
        } catch (TransferException $exception){
            Log::info("[GamSwitch][getOVABalance][".$session."] \t... 
            TransferException getting access token: ".$exception->getMessage());
        }catch (\Exception $exception){
            Log::info("[GamSwitch][getOVABalance][".$session."] \t... 
            Exception getting access token: ".$exception->getMessage());
            Log::info("[GamSwitch][getOVABalance][".$session."] \t "
                .$exception->getTraceAsString());
        }
    }


}