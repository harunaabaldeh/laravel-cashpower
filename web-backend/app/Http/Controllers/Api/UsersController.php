<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;


use App\User;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Jobs\AssignStarAccount;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\UtilitiesController;
use Carbon\Exceptions\ParseErrorException;



class UsersController extends Controller
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
            Log::notice("[API][UsersController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t called...");
            list($this->httpResponseArray, $this->statusCode) = [[
                    "code" => 200, 'trace_id' => $this->requestTraceId, "message" => "paginated user list",
                    "data" => User::latest()->with(['country'])->paginate(25),],200];

        } catch (Exception $e) {
            Log::error("[API][UsersController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][UsersController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                    "message" => "application error", "data" => []],400];
        }

        Log::info("[API][UsersController][index][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    /**
     * Display a listing of the resource.
     *
     * @param $accountNumber
     * @return JsonResponse
     */
    public function getUserByAccountNumberMatch($accountNumber)
    {
        try {
            $apiUser = JWTAuth::parseToken()->toUser();
            Log::notice("[API][UsersController][getUserByAccountNumberMatch][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t called... account Number: ".$accountNumber);

            if (!empty($accountNumber) && strlen($accountNumber) >= 3){
                $users = \App\User::where('star_account_number','LIKE','%'.$accountNumber.'%');
                if ($users->count() > 0){
                    list($this->httpResponseArray, $this->statusCode) = [[
                        "code" => 200, 'trace_id' => $this->requestTraceId, "message" => "positive user match found",
                        "data" => $users->with(['country'])->paginate(25),],200];
                }
            }


            if (empty($this->httpResponseArray) || empty($this->statusCode)){
                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                    "message" => "no valid match found", "data" => []],400];
            }

        } catch (Exception $e) {
            Log::error("[API][UsersController][getUserByAccountNumberMatch][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][UsersController][getUserByAccountNumberMatch][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                    "message" => "application error", "data" => []],400];
        }

        Log::info("[API][UsersController][getUserByAccountNumberMatch][" . $apiUser->id . "]"
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
            Log::notice("[API][UsersController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t called...\t".$requestContent);


            $requestPayload = json_decode($request->getContent(),true);

            if (!empty($requestPayload) && is_array($requestPayload)){


                if (!(
                    array_key_exists('firstname',$requestPayload) && !empty($requestPayload['firstname']) &&
                    array_key_exists('lastname',$requestPayload) && !empty($requestPayload['lastname']) &&
                    array_key_exists('dateOfBirth',$requestPayload) && !empty($requestPayload['dateOfBirth']) &&
                    array_key_exists('password',$requestPayload) && !empty($requestPayload['password'])
                    /*&& array_key_exists('idNumber',$requestPayload) && !empty($requestPayload['idNumber'])*/

                )){

                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "invalid request body ", "data" => []],400];

                }

                $parsedDateOfBirth = null;
                if (array_key_exists('dateOfBirth',$requestPayload)){
                    try{
                        $parsedDateOfBirth = Carbon::parse($requestPayload['dateOfBirth']);
                    }catch (ParseErrorException $exception){
                        Log::error("[API][UsersController][store]"
                            ."[" . $apiUser->id . "][" . $apiUser->email . "]" ."\t Date parse Exception \t"
                            .$exception->getMessage());
                    }catch (Exception $exception){
                        Log::error("[API][UsersController][store]"
                            ."[" . $apiUser->id . "][" . $apiUser->email . "]" ."\t Date parse Exception \t"
                            .$exception->getMessage());
                    }
                }

                if (empty($parsedDateOfBirth)){

                    Log::error("[API][UsersController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                        ."\tinvalid date object...\t");

                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "invalid date presentation",
                        "data" => []],400];
                }

                $countryId = array_key_exists('country_id',$requestPayload) ?
                    trim($requestPayload['country_id']) : null;

                $country = UtilitiesController::resolveCountry($countryId);

                if (empty($country)){
                    Log::error("[API][UsersController][store][" . $apiUser->id . "]"
                        ."[" . $apiUser->email . "][".$this->requestTraceId."]\t invalid country for id ...\t"
                        .$countryId);

                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "invalid country id", "data" => []],400];
                }


                $msisdn = $requestPayload['msisdn'];
                if (!UtilitiesController::isValidMSISDN($msisdn,$country->iso_3166_2)){

                    Log::error("[API][UsersController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                        ."\t invalid msisdn ...\t".$msisdn);
                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "invalid user msisdn", "data" => []],400];
                }


                $msisdn = UtilitiesController::formatMSISDN($msisdn,$country->iso_3166_2);
                $msisdn = str_replace("+","",str_replace(" ","",$msisdn));

                Log::error("[API][UsersController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                    ."\t final msisdn ...\t".$msisdn);


                if (\App\User::where('msisdn',$msisdn)->count() > 0){
                    Log::error("[API][UsersController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                        ."[".$this->requestTraceId."]\t user already exists already exiting...\t");

                    list($this->httpResponseArray, $this->statusCode) = [["code" => 409,
                        'trace_id' => $this->requestTraceId, "message" => "msisdn already taken", "data" => []],409];
                }



                $requestPayload = Arr::except($requestPayload,['balance','star_account_number','email_verified_at',
                    'uuid']);

                $requestPayload['balance'] = 0.0;
                $requestPayload['country_id'] = $country->id;
                $requestPayload['uuid'] = Uuid::uuid4()->toString();
                $requestPayload['password'] = Hash::make($requestPayload['password']);
                $requestPayload['api_token'] = hash('sha256', Uuid::uuid4()->toString());

                try {
                    $user = User::create($requestPayload);

                    dispatch(new AssignStarAccount($user));

                    if (!empty($user->email)){
                        $user->sendEmailVerificationNotification();
                    }

                    list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                        'trace_id' => $this->requestTraceId, "message" => "user created successfully",
                        "data" => $user],200];


                } catch (Exception $e) {
                    Log::error("[API][UsersController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                        ."[".$this->requestTraceId."]\t User Creation Error...".$e->getMessage());
                    Log::error("[API][UsersController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                        ."[".$this->requestTraceId."]\t User Creation Error...".$e->getTraceAsString());

                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "user creation error", "data" => []],400];
                }
            }else{
                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                    'trace_id' => $this->requestTraceId, "message" => "invalid request body", "data" => []],400];
            }
        } catch (Exception $e) {
            Log::error("[API][UsersController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][UsersController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }


        Log::info("[API][UsersController][store][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }


    /**
     * Increment User account after successful debit
     *
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     */
    public function doAccountTopUp(User  $user, Request $request)
    {
        try {

            $requestContent = str_replace("   ","",
                str_replace("\n", "", $request->getContent())
            );

            $apiUser = JWTAuth::parseToken()->toUser();
            Log::notice("[API][UsersController][doAccountTopUp][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t called...\t".$requestContent);


            $requestPayload = json_decode($request->getContent(),true);

            if (!empty($requestPayload) && is_array($requestPayload)){


                if (!(
                    array_key_exists('amount',$requestPayload) && !empty($requestPayload['amount']) &&
                    array_key_exists('currency',$requestPayload) && !empty($requestPayload['currency']) &&
                    array_key_exists('reference',$requestPayload) && !empty($requestPayload['reference'])
                )){
                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "invalid request body ", "data" => []],400];
                }


                $topUpAmount = floatval(trim($requestPayload['amount']));


                if (empty($user)){
                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "invalid user details ", "data" => []],400];
                }



                if (empty($topUpAmount)){
                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "invalid missing parameters",
                        "data" => []],400];
                }



                $message = "Account Top Up. of ".$requestPayload['currency'].": ".$topUpAmount.
                    " completed successfully to cartis pay account-number ".$user->star_account_number;


                try {
                    $fund = UtilitiesController::creditUserWallet($user,$topUpAmount,$message,
                        null,null);


                    list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                        'trace_id' => $this->requestTraceId, "message" => "account top-up completed successfully ",
                        "data" => ['fundingRecord' => $fund, 'user' => $user]],200];
                } catch (Exception $e) {
                    Log::error("[API][UsersController][doAccountTopUp][" . $apiUser->id . "]"
                        ."[" . $apiUser->email . "][".$this->requestTraceId."]\t"
                        ."Account Credit Error...".$e->getMessage());


                    Log::error("[API][UsersController][doAccountTopUp][" . $apiUser->id . "]"
                        ."[" . $apiUser->email . "][".$this->requestTraceId."]\t "
                        ."Account Credit Error...".$e->getTraceAsString());

                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "account credit error", "data" => []],400];
                }
            }else{
                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                    'trace_id' => $this->requestTraceId, "message" => "invalid request body", "data" => []],400];
            }
        } catch (Exception $e) {
            Log::error("[API][UsersController][doAccountTopUp][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][UsersController][doAccountTopUp][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }


        Log::info("[API][UsersController][doAccountTopUp][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    /**
     * Decrease User account balance to process transaction
     *
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     */
    public function doAccountDebit(User  $user, Request $request)
    {
        try {

            $requestContent = str_replace("   ","",
                str_replace("\n", "", $request->getContent())
            );

            $apiUser = JWTAuth::parseToken()->toUser();
            Log::notice("[API][UsersController][doAccountDebit][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t called...\t".$requestContent);


            $requestPayload = json_decode($request->getContent(),true);

            if (!empty($requestPayload) && is_array($requestPayload)){


                if (!(
                    array_key_exists('amount',$requestPayload) && !empty($requestPayload['amount']) &&
                    array_key_exists('currency',$requestPayload) && !empty($requestPayload['currency']) &&
                    array_key_exists('reference',$requestPayload) && !empty($requestPayload['reference'])
                )){
                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "invalid request body ", "data" => []],400];
                }


                $debitAmount = floatval(trim($requestPayload['amount']));


                if (empty($user)){
                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "invalid user details ", "data" => []],400];
                }



                if (empty($debitAmount)){
                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "invalid missing parameters",
                        "data" => []],400];
                }


                try {

                    if (floatval($user->balance) >= $debitAmount){
                        $fund = UtilitiesController::debitUserWalletWithoutTransaction($user,$debitAmount);


                        list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                            'trace_id' => $this->requestTraceId, "message" => "account debited completed successfully",
                            "data" => ['fundingRecord' => $fund, 'user' => $user]],200];
                    }else{
                        list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                            'trace_id' => $this->requestTraceId, "message" => "insufficient funds", "data" => []],400];
                    }

                } catch (Exception $e) {
                    Log::error("[API][UsersController][doAccountDebit][" . $apiUser->id . "]"
                        ."[" . $apiUser->email . "][".$this->requestTraceId."]\t"
                        ."Account Credit Error...".$e->getMessage());


                    Log::error("[API][UsersController][doAccountDebit][" . $apiUser->id . "]"
                        ."[" . $apiUser->email . "][".$this->requestTraceId."]\t "
                        ."Account Debit Error...".$e->getTraceAsString());

                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "account debit error", "data" => []],400];
                }
            }else{
                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                    'trace_id' => $this->requestTraceId, "message" => "invalid request body", "data" => []],400];
            }
        } catch (Exception $e) {
            Log::error("[API][UsersController][doAccountDebit][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][UsersController][doAccountDebit][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }


        Log::info("[API][UsersController][doAccountDebit][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user)
    {
        $apiUser = JWTAuth::parseToken()->toUser();
        try
        {
            list($this->httpResponseArray, $this->statusCode) =
                [["code" => 200, "message" => "user found", "data" => $user],200];
        } catch (Exception $e) {
            Log::error("[API][UsersController][show][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][UsersController][show][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) =
                [["code" => 400, "message" => "application error", "data" => []],400];
        }

        Log::info("[API][UsersController][show][" . $apiUser->id . "][" . $apiUser->email . "]"
            ."[" . $this->requestTraceId . "]\t final http response ...\t status: "
            . $this->statusCode . "\t", $this->httpResponseArray);
        return response()->json($this->httpResponseArray,$this->statusCode);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(Request $request, User $user)
    {
        $apiUser = JWTAuth::parseToken()->toUser();
        try {
            $requestContent = str_replace("   ","",
                str_replace("\n", "", $request->getContent())
            );
            Log::notice("[API][UsersController][update][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[" . $this->requestTraceId . "]\t called...user-id: ".$user->id."\t". $requestContent);

            $requestPayload = json_decode($request->getContent(),true);

            if (!empty($requestPayload) && is_array($requestPayload)){

                $requestPayload = Arr::except($requestPayload,['balance','star_account_number','email_verified_at',
                    'uuid','msisdn','country_id']);

                if (array_key_exists('password',$requestPayload)){
                    $requestPayload['password'] = Hash::make($requestPayload['password']);
                }


                try {
                    $user = $user->update($requestPayload);

                    list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                        'trace_id' => $this->requestTraceId, "message" => "user updated successfully",
                        "data" => $user],200];

                } catch (Exception $e) {

                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "user update error", "data" => []],400];
                }
            }else{

                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                    "message" => "invalid request body", "data" => []],400];

            }

        } catch (Exception $e) {
            Log::error("[API][UsersController][update][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[" . $this->requestTraceId . "]\t Error...".$e->getMessage());
            Log::error("[API][UsersController][update][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[" . $this->requestTraceId . "]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }


        Log::info("[API][UsersController][update][" . $apiUser->id . "][" . $apiUser->email . "]"
            ."[" . $this->requestTraceId . "]\t final http response ...\t status: "
            . $this->statusCode . "\t", $this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user)
    {
        $apiUser = JWTAuth::parseToken()->toUser();

        try {

            Log::notice("[API][UsersController][destroy][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[" . $this->requestTraceId . "]\t called...\t", $user->toArray());
            $user->delete();

            list($this->httpResponseArray, $this->statusCode) = [["code" => 200, 'trace_id' => $this->requestTraceId,
                "message" => "user removed successfully", "data" => null], 200];

        } catch (Exception $e) {
            Log::error("[API][UsersController][destroy][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[" . $this->requestTraceId . "]\t Error...".$e->getMessage());
            Log::error("[API][UsersController][destroy][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[" . $this->requestTraceId . "]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }

        Log::info("[API][UsersController][destroy][" . $apiUser->id . "][" . $apiUser->email . "]"
            ."[" . $this->requestTraceId . "]\t final http response ...\t status: "
            . $this->statusCode . "\t", $this->httpResponseArray);
        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    /**
     * check if a user exists for a given
     * msisdn/password pair
     * @param Request $request
     * @return JsonResponse
     */
    public function authenticate(Request  $request)
    {

        $apiUser = JWTAuth::parseToken()->toUser();
        try
        {

            $requestContent = str_replace("   ","",
                str_replace("\n", "", $request->getContent())
            );

            Log::info("[API][UsersController][authenticate][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[" . $this->requestTraceId . "]\t called ...\t ".Crypt::encrypt($requestContent));


            $requestPayload = json_decode($request->getContent(),true);

            if (!empty($requestPayload) && is_array($requestPayload)){

                if (
                    array_key_exists('msisdn',$requestPayload) && !empty($requestPayload['msisdn']) &&
                    array_key_exists('country_id',$requestPayload) && !empty($requestPayload['country_id']) &&
                    array_key_exists('password',$requestPayload) && !empty($requestPayload['password'])
                ){

                    $countryId = trim($requestPayload['country_id']);

                    $country = UtilitiesController::resolveCountry($countryId);

                    if (empty($country)){
                        Log::error("[API][UsersController][authenticate][" . $apiUser->id . "]"
                            ."[" . $apiUser->email . "][".$this->requestTraceId."]\t invalid country for id ...\t"
                            .$countryId);

                        list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                            'trace_id' => $this->requestTraceId, "message" => "invalid country id",
                            "data" => []],400];
                    }


                    $msisdn = $requestPayload['msisdn'];
                    if (!UtilitiesController::isValidMSISDN($msisdn,$country->iso_3166_2)){

                        Log::error("[API][UsersController][authenticate][" . $apiUser->id . "]"
                            ."[" . $apiUser->email . "]\t invalid msisdn ...\t".$msisdn);

                        list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                            'trace_id' => $this->requestTraceId, "message" => "invalid user msisdn", "data" => []],400];

                    }


                    $msisdn = UtilitiesController::formatMSISDN($msisdn,$country->iso_3166_2);
                    $msisdn = str_replace("+","",str_replace(" ","",$msisdn));


                    Log::info("[API][UsersController][authenticate][" . $apiUser->id . "]"
                        ."[" . $apiUser->email . "]\t final formatted msisdn ...\t".$msisdn);

                    $user = \App\User::where('msisdn',$msisdn)->with(['country'])->first();

                    Log::info("[API][UsersController][authenticate][" . $apiUser->id . "]"
                        ."[" . $apiUser->email . "]\t user object before password check ...\t",
                        (!empty($user) ? $user->toArray() : []));

                    if (!empty($user) && $user->accountStatus != "active"){
                        list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                            'trace_id' => $this->requestTraceId, "message" => "in-active account status",
                            "data" => null],400];
                    }

                    if (!empty($user) && Hash::check($requestPayload['password'],$user->password)){
                        list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                            'trace_id' => $this->requestTraceId, "message" => "authentication success",
                            "data" => $user],200];
                    }else{
                        list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                            'trace_id' => $this->requestTraceId, "message" => "authentication failure",
                            "data" => null],400];
                    }
                }else{
                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "invalid request body", "data" => null],400];
                }
            }else
            {
                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                    'trace_id' => $this->requestTraceId, "message" => "invalid request body", "data" => null],400];
            }

        } catch (Exception $e) {
            Log::error("[API][UsersController][authenticate][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][UsersController][authenticate][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => null],400];
        }

        Log::info("[API][UsersController][authenticate][" . $apiUser->id . "][" . $apiUser->email . "]"
            ."[" . $this->requestTraceId . "]\t final http response ...\t status: "
            . $this->statusCode . "\t", $this->httpResponseArray);
        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    public function purgeUser($msisdn){

        $user = User::where('msisdn',$msisdn)->first();
        if ($user){
            \App\Fund::whereUserId($user->id)->forceDelete();
            \App\Payment::whereUserId($user->id)->forceDelete();
            \App\Transaction::whereUserId($user->id)->forceDelete();

            try {
                $bulk = new \MongoDB\Driver\BulkWrite;
                $msisdn = Str::startswith($user->msisdn,"+") ? $user->msisdn  : "+".$user->msisdn;
                $bulk->delete(['phoneNumber' => $msisdn], ['limit' => 1]);
                $manager = new \MongoDB\Driver\Manager('mongodb://localhost:27017');
                $manager->executeBulkWrite('star-pay.user', $bulk);
            }catch (Exception $exception){
                Log::error("[API][UsersController][purgeUser] \t Error removing user \t".$user->id);
            }
            return  "Done";
        }else{
            return "User note found";
        }

    }
}
