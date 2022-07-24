<?php

namespace App\Http\Controllers\Api;


use App\Bank;
use App\Beneficiary;
use App\User;
use Exception;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilitiesController;


class BeneficiariesController extends Controller
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
            Log::notice("[API][BeneficiariesController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t called...");
            list($this->httpResponseArray, $this->statusCode) = [[
                "code" => 200, 'trace_id' => $this->requestTraceId, "message" => "paginated beneficiary list",
                "data" => Beneficiary::latest()->with(['country'])->paginate(25),],200];

        } catch (Exception $e) {
            Log::error("[API][BeneficiariesController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][BeneficiariesController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }

        Log::info("[API][BeneficiariesController][index][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }


    /**
     * Display a listing of the resource.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function getUserBeneficiaries(User  $user)
    {
        try {
            $apiUser = JWTAuth::parseToken()->toUser();
            Log::notice("[API][BeneficiariesController][getUserBeneficiaries][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t called...");

            $beneficiaries = Beneficiary::where('user_id',$user->id);

            if ($beneficiaries->count() > 0){
                list($this->httpResponseArray, $this->statusCode) = [[
                    "code" => 200, 'trace_id' => $this->requestTraceId, "message" => "paginated beneficiary list for "
                        .$user->fullName, "data" => $beneficiaries->latest()->paginate(25),],200];

            }else{
                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                    "message" => "no beneficiaries found for user ", "data" => []],400];
            }

        } catch (Exception $e) {
            Log::error("[API][BeneficiariesController][getUserBeneficiaries][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][BeneficiariesController][getUserBeneficiaries][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }

        Log::info("[API][BeneficiariesController][getUserBeneficiaries][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $requestContent = str_replace("   ","",
                str_replace("\n", "", $request->getContent())
            );

            $apiUser = JWTAuth::parseToken()->toUser();
            Log::notice("[API][BeneficiariesController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t called...\t".$requestContent);


            $requestPayload = json_decode($request->getContent(),true);

            if (!empty($requestPayload) && is_array($requestPayload)){


                if (!(
                    array_key_exists('account_type',$requestPayload) && !empty($requestPayload['account_type']) &&
                    array_key_exists('firstname',$requestPayload) && !empty($requestPayload['firstname']) &&
                    array_key_exists('lastname',$requestPayload) && !empty($requestPayload['lastname']) &&
                    array_key_exists('country_id',$requestPayload) && !empty($requestPayload['country_id']) &&
                    array_key_exists('user_id',$requestPayload) && !empty($requestPayload['user_id'])

                )){

                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "invalid request body ", "data" => []],400];

                }

                if (!array_key_exists('nickname',$requestPayload) ||empty($requestPayload['nickname']) ){
                    $requestPayload['nickname'] = $requestPayload['firstname'];
                }
                $user = \App\User::find($requestPayload['user_id']);

                if (empty($user)){
                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "No Valid Resource found for user",
                        "data" => []],400];
                }


                $countryId = trim($requestPayload['country_id']);
                $country = UtilitiesController::resolveCountry($countryId);

                if (empty($country)){
                    Log::error("[API][BeneficiariesController][store][" . $apiUser->id . "]"
                        ."[" . $apiUser->email . "][".$this->requestTraceId."]\t invalid country for id ...\t"
                        .$countryId);

                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "invalid country id",
                        "data" => []],400];
                }



                $msisdn = null;
                if (array_key_exists('msisdn',$requestPayload) && !empty($requestPayload['msisdn'])){
                    $msisdn = $requestPayload['msisdn'];
                    if (!UtilitiesController::isValidMSISDN($msisdn,$country->iso_3166_2)){
                        Log::error("[API][BeneficiariesController][store][" . $apiUser->id . "]"
                            ."[" . $apiUser->email . "]\t invalid msisdn ...\t".$msisdn);
                        list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                            'trace_id' => $this->requestTraceId,
                            "message" => "invalid user msisdn", "data" => []],400];
                    }


                    $msisdn = UtilitiesController::formatMSISDN($msisdn,$country->iso_3166_2);
                    $msisdn = str_replace("+","",str_replace(" ","",$msisdn));

                    Log::error("[API][BeneficiariesController][store][" . $apiUser->id . "]"
                        ."[" . $apiUser->email . "]\t final msisdn ...\t".$msisdn);

                }



                //do further checks given an account-type
                $accountType = $requestPayload['account_type'];
                switch ($accountType){
                    case "PickUp":
                    case "Wallet":
                        if (!array_key_exists('msisdn',$requestPayload) && !empty($requestPayload['msisdn'])){
                            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                                'trace_id' => $this->requestTraceId, "message" => "missing required",
                                "data" => []],400];
                        }


                    $existingBeneficiary = Beneficiary::where('msisdn', $msisdn)
                        ->whereAccountType('account_type',$requestPayload['account_type'])->whereUserId($user->id);
                    if ($existingBeneficiary->count() > 0){
                            list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                                'trace_id' => $this->requestTraceId,
                                "message" => "beneficiary with msisdn already exists", "data" =>
                                    $existingBeneficiary->first()],200];
                        }
                    break;
                    case "Bank":
                        if (!(array_key_exists('bank_name',$requestPayload) && !empty($requestPayload['bank_name'])
                            && array_key_exists('account_routing_number',$requestPayload)
                            && !empty($requestPayload['account_routing_number']) &&
                            array_key_exists('account_number',$requestPayload) &&
                            !empty($requestPayload['account_number'])))
                        {
                            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                                'trace_id' => $this->requestTraceId, "message" => "missing required",
                                "data" => []],400];
                        }

                        $bank = Bank::where('routing_code',$requestPayload['account_routing_number'])->first();

                        if (empty($bank)){
                            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                                'trace_id' => $this->requestTraceId,
                                "message" => "invalid routing number", "data" => []],400];
                        }else{
                            $existingBeneficiary = Beneficiary::
                            where('account_number', $requestPayload['account_number'])->whereUserId($user->id)
                                ->where('account_routing_number', $requestPayload['account_routing_number'])
                                ->whereAccountType('account_type',$requestPayload['account_type']);


                            if ($existingBeneficiary->count() > 0){

                                list($this->httpResponseArray, $this->statusCode) = [["code" => 409,
                                    'trace_id' => $this->requestTraceId,
                                    "message" => "beneficiary exists with account/routing number pair",
                                    "data" => $existingBeneficiary->first()],409];
                            }
                        }



                        break;
                }

                // no error message has been assigned above
                if (empty($this->httpResponseArray)){

                    try {
                        $beneficiary = Beneficiary::create($requestPayload);

                       if (in_array($beneficiary->account_type,['PickUp','Wallet'])
                           && empty($beneficiary->account_number)){
                           $beneficiary->account_number = $beneficiary->msisdn;
                           $beneficiary->save();
                       }


                        list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                            'trace_id' => $this->requestTraceId, "message" => "beneficiary created successfully",
                            "data" => $beneficiary],200];


                    } catch (Exception $e) {
                        Log::error("[API][BeneficiariesController][store][" . $apiUser->id . "]"
                            ."[" . $apiUser->email . "][".$this->requestTraceId."]\t beneficiary Creation Error..."
                            .$e->getMessage());
                        Log::error("[API][BeneficiariesController][store][" . $apiUser->id . "]"
                            ."[" . $apiUser->email . "][".$this->requestTraceId."]\t beneficiary Creation Error...".
                            $e->getTraceAsString());

                        list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                            'trace_id' => $this->requestTraceId, "message" => "beneficiary creation error",
                            "data" => []],400];
                    }
                }

            }else{
                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                    'trace_id' => $this->requestTraceId, "message" => "invalid request body", "data" => []],400];
            }
        } catch (Exception $e) {
            Log::error("[API][BeneficiariesController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][BeneficiariesController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }


        Log::info("[API][BeneficiariesController][store][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    /**
     * Display the specified resource.
     *
     * @param Beneficiary $beneficiary
     * @return JsonResponse
     */
    public function show(Beneficiary $beneficiary)
    {
        $apiUser = JWTAuth::parseToken()->toUser();
        try
        {
            list($this->httpResponseArray, $this->statusCode) =
                [["code" => 200, "message" => "Beneficiary found", "data" => $beneficiary],200];
        } catch (Exception $e) {
            Log::error("[API][BeneficiariesController][show][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][BeneficiariesController][show][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) =
                [["code" => 400, "message" => "application error", "data" => []],400];
        }

        Log::info("[API][BeneficiariesController][show][" . $apiUser->id . "][" . $apiUser->email . "]"
            ."[" . $this->requestTraceId . "]\t final http response ...\t status: "
            . $this->statusCode . "\t", $this->httpResponseArray);
        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Beneficiary $beneficiary
     * @return JsonResponse
     */
    public function update(Request $request, Beneficiary $beneficiary)
    {
        $apiUser = JWTAuth::parseToken()->toUser();
        try {
            $requestContent = str_replace("   ","",
                str_replace("\n", "", $request->getContent())
            );
            Log::notice("[API][BeneficiariesController][update][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[" . $this->requestTraceId . "]\t called...user-id: ".$beneficiary->id."\t". $requestContent);

            $requestPayload = json_decode($request->getContent(),true);

            if (!empty($requestPayload) && is_array($requestPayload)){




                try {

                    if (array_key_exists('country_id',$requestPayload) && !empty($requestPayload['country_id'])){


                        $country = UtilitiesController::resolveCountry(trim(($requestPayload['country_id'])));


                        if (empty($country)){
                            Log::error("[API][BeneficiariesController][update][" . $apiUser->id . "]"
                                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t invalid country for id \t"
                                .$requestPayload['country_id']);

                            $requestPayload['country_id'] = $country->id;
                            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                                'trace_id' => $this->requestTraceId, "message" => "invalid country id",
                                "data" => []],400];
                        }

                        $msisdn = null;
                        if (array_key_exists('msisdn',$requestPayload) && !empty($requestPayload['msisdn'])){
                            $msisdn = $requestPayload['msisdn'];
                            if (!UtilitiesController::isValidMSISDN($msisdn,$country->iso_3166_2)){
                                Log::error("[API][BeneficiariesController][update][" . $apiUser->id . "]"
                                    ."[" . $apiUser->email . "]\t invalid msisdn ...\t".$msisdn);
                                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                                    'trace_id' => $this->requestTraceId,
                                    "message" => "invalid user msisdn", "data" => []],400];
                            }


                            $msisdn = UtilitiesController::formatMSISDN($msisdn,$country->iso_3166_2);
                            $msisdn = str_replace("+","",str_replace(" ","",$msisdn));

                            Log::error("[API][BeneficiariesController][update][" . $apiUser->id . "]"
                                ."[" . $apiUser->email . "]\t final msisdn ...\t".$msisdn);

                            $requestPayload['msisdn'] = $msisdn;
                        }

                    }

                    if (array_key_exists('account_type',$requestPayload) &&
                        $requestPayload['account_type'] == "Bank"){
                        if (array_key_exists('account_routing_number',$requestPayload)
                            && !empty($requestPayload['account_routing_number']))
                        {

                            $bank = Bank::where('routing_code',$requestPayload['account_routing_number'])
                                ->first();

                            if (empty($bank)){
                                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                                    'trace_id' => $this->requestTraceId,
                                    "message" => "invalid routing number", "data" => []],400];
                            }
                        }
                    }

                    if (empty($this->httpResponseArray)){

                        $requestPayload = Arr::except($requestPayload,['account_type','user_id']);
                        $beneficiary = $beneficiary->update($requestPayload);

                        list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                            'trace_id' => $this->requestTraceId, "message" => "beneficiary updated successfully",
                            "data" => $beneficiary],200];
                    }

                } catch (Exception $e) {

                    Log::error("[API][BeneficiariesController][update][" . $apiUser->id . "]"
                        ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t beneficiary update error..."
                        .$e->getMessage());
                    Log::error("[API][BeneficiariesController][update][" . $apiUser->id . "]"
                        ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t beneficiary update error..."
                        .$e->getTraceAsString());

                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "beneficiary update error",
                        "data" => []],400];
                }
            }else{

                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                    "message" => "invalid request body", "data" => []],400];

            }

        } catch (Exception $e) {
            Log::error("[API][BeneficiariesController][update][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[" . $this->requestTraceId . "]\t Error...".$e->getMessage());
            Log::error("[API][BeneficiariesController][update][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[" . $this->requestTraceId . "]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }


        Log::info("[API][BeneficiariesController][update][" . $apiUser->id . "][" . $apiUser->email . "]"
            ."[" . $this->requestTraceId . "]\t final http response ...\t status: "
            . $this->statusCode . "\t", $this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Beneficiary $beneficiary
     * @return JsonResponse
     */
    public function destroy(Beneficiary $beneficiary)
    {
        $apiUser = JWTAuth::parseToken()->toUser();

        try {

            Log::notice("[API][BeneficiariesController][destroy][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[" . $this->requestTraceId . "]\t called...\t", $beneficiary->toArray());
            $beneficiary->delete();

            list($this->httpResponseArray, $this->statusCode) = [["code" => 200, 'trace_id' => $this->requestTraceId,
                "message" => "beneficiary removed successfully", "data" => null], 200];

        } catch (Exception $e) {
            Log::error("[API][BeneficiariesController][destroy][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[" . $this->requestTraceId . "]\t Error...".$e->getMessage());
            Log::error("[API][BeneficiariesController][destroy][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[" . $this->requestTraceId . "]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }

        Log::info("[API][BeneficiariesController][destroy][" . $apiUser->id . "][" . $apiUser->email . "]"
            ."[" . $this->requestTraceId . "]\t final http response ...\t status: "
            . $this->statusCode . "\t", $this->httpResponseArray);
        return response()->json($this->httpResponseArray,$this->statusCode);
    }

}
