<?php

namespace App\Http\Controllers\Api;

use App\Bank;
use App\Beneficiary;
use App\Biller;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilitiesController;
use App\Rate;
use App\ServiceTypeCountryConfiguration;
use App\User;
use App\Utils\GamSwitch;
use App\Utils\Zeepay;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Tymon\JWTAuth\Facades\JWTAuth;

class BillersController extends Controller
{
    private  $requestTraceId, $statusCode, $httpResponseArray;
    public function __construct()
    {
        $this->statusCode = 400;
        $this->httpResponseArray = [];
        $this->middleware('auth:api');
        $this->requestTraceId = Uuid::uuid4()->toString();
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $apiUser = JWTAuth::parseToken()->toUser();
            Log::notice("[API][BillersController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t called...");
            list($this->httpResponseArray, $this->statusCode) = [[
                "code" => 200, 'trace_id' => $this->requestTraceId, "message" => "paginated billers list",
                "data" => \App\Biller::latest()->get(['name','uuid','category','country_id']),],200];

        } catch (Exception $e) {
            Log::error("[API][BillersController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][BillersController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }

        Log::info("[API][BillersController][index][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    public function getBillersByCountry($countryId){
        $apiUser = JWTAuth::parseToken()->toUser();
        try {

            Log::info("[API][BillersController][getBillersByCountry][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t called ...country-id: ".$countryId);

            $country = UtilitiesController::resolveCountry($countryId);

            if (empty($country)){
                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                    'trace_id' => $this->requestTraceId, "message" => "invalid country code", "data" => null], 400];
            }


            $billers = Biller::where('country_id',$country->id);

            if($billers->count() > 0){
                list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                    'trace_id' => $this->requestTraceId, "message" => "billers list for ".$country->iso_3166_3,
                    "data" => $billers->get(['name','uuid','category','country_id'])], 200];

            }else{
                list($this->httpResponseArray, $this->statusCode) =
                    [
                        [
                            "code" => 400,
                            'trace_id' => $this->requestTraceId,
                            "message" => "no banks found for ".$country->iso_3166_3,
                            "data" => null
                        ],
                        400
                    ];
            }

        } catch (\Exception $e) {
            Log::info("[API][BillersController][getBillersByCountry][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getMessage());
            Log::info("[API][BillersController][getBillersByCountry][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getTraceAsString());
            list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => null], 400];
        }

        Log::info("[API][BillersController][getBillersByCountry][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    /**
     * Handle Bill Payment Validation
     *
     * @param User $user
     * @param Biller $biller
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function validateBillPayment(User  $user, $billerId, Request $request): JsonResponse
    {
        try {

            $requestContent = str_replace("   ","",
                str_replace("\n", "", $request->getContent())
            );

            $apiUser = JWTAuth::parseToken()->toUser();
            Log::notice("[API][BillersController][validateBillPayment][" . $apiUser->id . "][" .
                $apiUser->email . "][".$this->requestTraceId."]\t called...\t".$requestContent);

            Log::notice("[API][BillersController][validateBillPayment][" . $apiUser->id . "][" .
                $apiUser->email . "][".$this->requestTraceId."]\t User...\t",$user->toArray());

            $biller = \App\Biller::where('uuid',$billerId)->first();

            if (empty($biller)){
                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                    'trace_id' => $this->requestTraceId, "message" => "invalid biller details",
                    "data" => []],400];
            }else{

                Log::notice("[API][BillersController][validateBillPayment][" . $apiUser->id . "][" .
                    $apiUser->email . "][".$this->requestTraceId."]\t User...\t",$biller->toArray());


                $requestPayload = json_decode($request->getContent(),true);

                if (!empty($requestPayload) && is_array($requestPayload)){


                    if (!(
                        array_key_exists('destination_account',$requestPayload) &&
                        !empty($requestPayload['destination_account']) &&
                        array_key_exists('reference',$requestPayload) && !empty($requestPayload['reference']) &&
                        array_key_exists('receive_amount',$requestPayload) &&
                        !empty($requestPayload['receive_amount'])
                        && array_key_exists('send_amount',$requestPayload) &&
                        !empty($requestPayload['send_amount'])
                    )){

                        list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                            'trace_id' => $this->requestTraceId,
                            "message" => "invalid request body", "data" => []],400];

                    }else{
                        $sourceCountry = $user->country;
                        $destinationCountry = $biller->country;

                        $reference = $user->id."-".$biller->id."-".Uuid::uuid4()->toString();

                        if (empty($sourceCountry) || empty($destinationCountry)){
                            Log::error("[API][BillersController][validateBillPayment][" . $apiUser->id . "]"
                                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t invalid country for".
                                " destination  or send countries ...\t" .trim($requestPayload['source_country'])."\t"
                                .trim($requestPayload['destination_country']));

                            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                                'trace_id' => $this->requestTraceId,
                                "message" => "invalid destination or send country id", "data" => []],400];
                        }

                        switch ($destinationCountry->iso_3166_3){
                            case "GHA":
                                $validationPayload = [
                                    "destination_account" => $requestPayload['destination_account'],
                                    "payer_name" => trim($user->fullName),
                                    "receive_currency" => $destinationCountry->currency_code,
                                    "send_currency" => $sourceCountry->currency_code,
                                    "receive_country" => $destinationCountry->iso_3166_3,
                                    "send_country" => $sourceCountry->iso_3166_3,
                                    "reference" => $reference,
                                    "receive_amount" => $requestPayload['receive_amount'],
                                    "send_amount" => $requestPayload['send_amount'],

                                ];

                                $zeepayResponse = Zeepay::handleBillPaymentValidation($biller,$validationPayload);


                                if (!empty($zeepayResponse) && is_array($zeepayResponse) &&
                                    array_key_exists('zeepay_id', $zeepayResponse)){
                                    \Redis::set($reference,$zeepayResponse['zeepay_id']);
                                    \Redis::expire($reference,now()->addDay()->diffInSeconds(now()));

                                    $zeepayResponse['reference'] = $reference;
                                    list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                                        'trace_id' => $this->requestTraceId, "message" => "validation successful",
                                        "data" => [Arr::except($zeepayResponse,'zeepay_id')]],200];
                                }else{
                                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                                        'trace_id' => $this->requestTraceId, "message" => "biller validation failed",
                                        "data" => []],400];
                                }

                                break;
                            case "GMB":
                                $accountDetails = explode("_",trim($requestPayload['destination_account']));

                                $validationResponse = GamSwitch::validateMeterNumber(8000,$accountDetails[0],
                                    $accountDetails[1]);

                                if (!empty($validationResponse) && is_array($validationResponse) &&
                                    array_key_exists(0,$validationResponse) && $validationResponse[0] == "success")
                                {
                                    list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                                        'trace_id' => $this->requestTraceId, "message" => "validation successful",
                                        "data" => ["account_name" => $validationResponse[2],
                                            "account" => $accountDetails[0],"amount"=> false, "reference" => $reference]
                                    ],200];
                                }else{
                                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                                        'trace_id' => $this->requestTraceId, "message" => "biller validation failed",
                                        "data" => []],400];
                                }

                                break;
                        }

                    }


                }else{
                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "invalid request body", "data" => []],400];
                }
            }


        } catch (Exception $e) {
            Log::error("[API][BillersController][validateBillPayment][" . $apiUser->id . "][" .
                $apiUser->email . "][".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][BillersController][validateBillPayment][" . $apiUser->id . "][" .
                $apiUser->email . "][".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }


        Log::info("[API][BillersController][validateBillPayment][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    /**
     * Complete Bill Payment Transactions
     *
     * @param User $user
     * @param Biller $biller
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function completeBillPaymentTransaction(User  $user, Beneficiary  $beneficiary, Rate  $rate,
                                                   $billerId, $reference, Request $request): JsonResponse
    {
        try {

            $requestContent = str_replace("   ","",
                str_replace("\n", "", $request->getContent())
            );

            $apiUser = JWTAuth::parseToken()->toUser();
            Log::notice("[API][BillersController][completeBillPaymentTransaction][" . $apiUser->id . "][" .
                $apiUser->email . "][".$this->requestTraceId."]\t called...\t".$requestContent);

            Log::notice("[API][BillersController][completeBillPaymentTransaction][" . $apiUser->id . "][" .
                $apiUser->email . "][".$this->requestTraceId."]\t User...\t",$user->toArray());

            $biller = \App\Biller::where('uuid',$billerId)->first();

            if (empty($biller)){
                Log::notice("[API][BillersController][completeBillPaymentTransaction][" . $apiUser->id . "][" .
                    $apiUser->email . "][".$this->requestTraceId."]\t biller object empty...\t");

                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                    'trace_id' => $this->requestTraceId, "message" => "invalid biller details",
                    "data" => []],400];
            }else{
                Log::notice("[API][BillersController][completeBillPaymentTransaction][" . $apiUser->id . "][" .
                    $apiUser->email . "][".$this->requestTraceId."]\t User...\t",$biller->toArray());


                $requestPayload = json_decode($request->getContent(),true);

                if (!empty($requestPayload) && is_array($requestPayload)){


                    if (!(
                        array_key_exists('destination_account',$requestPayload) &&
                        !empty($requestPayload['destination_account']) &&
                        array_key_exists('receive_amount',$requestPayload) &&
                        !empty($requestPayload['receive_amount'])
                        && array_key_exists('send_amount',$requestPayload) &&
                        !empty($requestPayload['send_amount'])
                        && array_key_exists('purpose',$requestPayload) && !empty($requestPayload['purpose'])
                    )){

                        Log::notice("[API][BillersController][completeBillPaymentTransaction][" . $apiUser->id
                            . "][" . $apiUser->email . "][".$this->requestTraceId."]\t invalid or missing params...\t");

                        list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                            'trace_id' => $this->requestTraceId,
                            "message" => "invalid request body", "data" => []],400];

                    }else{
                        $destinationCountry = $biller->country;
                        $sourceCountry = $user->country;


                        $transaction = \App\Transaction::create([
                            'type' => 'bill','source_currency' => $sourceCountry->currency_code,
                            'destination_currency' => $destinationCountry->currency_code,'rate_id' => $rate->id,
                            'source_amount' => $requestPayload['send_amount'],
                            'beneficiary_id' => $beneficiary->id, 'purpose' => $requestPayload['purpose'],
                            'destination_amount' => $requestPayload['receive_amount'],'user_id' => $user->id,
                        ]);


                        UtilitiesController::debitUserWallet($user,$transaction);
                        switch ($destinationCountry->iso_3166_3){
                            case "GHA":

                                $transaction->reference = $requestPayload['reference'];
                                $transaction->save();
                                //TODO handle star pay to star pay differently
//                                dispatch(new \App\Jobs\TransactionsDispatcher($transaction));

                                //TODO extract this into the TransactionsDispatcher Job...
                                Zeepay::completeBillPayment($biller,$reference,$requestPayload);
                                list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                                    'trace_id' => $this->requestTraceId,
                                    "message" => "transaction dispatched Successfully",
                                    "data" => []],200];

                                break;
                            case "GMB":

//                                $accountDetails = explode("_",trim($requestPayload['destination_account']));
                                $transaction->reference = $requestPayload['reference'];
                                $transaction->save();
                                GamSwitch::doElectricityTopUp($transaction, $beneficiary);
                                list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                                    'trace_id' => $this->requestTraceId,
                                    "message" => "transaction dispatched Successfully",
                                    "data" => []],200];

                                break;
                        }

                    }


                }else{
                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "invalid request body", "data" => []],400];
                }
            }


        } catch (Exception $e) {
            Log::error("[API][BillersController][completeBillPaymentTransaction][" . $apiUser->id . "][" .
                $apiUser->email . "][".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][BillersController][completeBillPaymentTransaction][" . $apiUser->id . "][" .
                $apiUser->email . "][".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }


        Log::info("[API][BillersController][completeBillPaymentTransaction][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    /**
     * Handle airtime validation packages
     * @param $countryCode
     * @param $msisdn
     * @return JsonResponse
     */
    public function getAllowedAirtimePackages($countryCode, $msisdn){

        $apiUser = JWTAuth::parseToken()->toUser();
        try {

            Log::info("[API][BillersController][getAllowedAirtimePackages][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t called ...country-id: ".$countryCode);

            $country = UtilitiesController::resolveCountry($countryCode);

            if (empty($country)){
                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                    'trace_id' => $this->requestTraceId, "message" => "invalid country code", "data" => null], 400];
            }

            $service = ServiceTypeCountryConfiguration::where('service_name','Airtime Topup')->first();

            if (!empty($service)){
                if (in_array($country->id,$service->countries)){

                    $packageInformation = \Redis::hget('x-star-pay-airtime-packages',$msisdn);

                    if (!empty($packageInformation)){
                        $packageInformation = json_decode($packageInformation,true);

                        list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                            'trace_id' => $this->requestTraceId, "message" => "package list for ".$country->iso_3166_3,
                            "data" => $packageInformation['packages']], 200];

                    }else{
                        $packages  = explode(",",env("country_default_airtime_package_".
                            $country->iso_3166_3));


                        list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                            'trace_id' => $this->requestTraceId, "message" => "package list for ".$country->iso_3166_3,
                            "data" => $packages], 200];

                    }

                }else{
                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "country not whitelisted for service ",
                        "data" => null], 400];
                }
            }else{
                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                    'trace_id' => $this->requestTraceId, "message" => "Service Configuration Failure", "data" => null],
                    400];
            }

        } catch (\Exception $e) {
            Log::info("[API][BillersController][getAllowedAirtimePackages][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getMessage());
            Log::info("[API][BillersController][getAllowedAirtimePackages][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getTraceAsString());
            list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => null], 400];
        }

        Log::info("[API][BillersController][getAllowedAirtimePackages][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }
}
