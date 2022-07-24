<?php

namespace App\Http\Controllers\Api;

use App\Beneficiary;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilitiesController;
use App\Rate;
use App\Transaction;
use App\User;
use App\Utils\AppsMobile;
use App\Utils\Zeepay;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Tymon\JWTAuth\Facades\JWTAuth;

class TransactionsController extends Controller
{

    private  $requestTraceId, $statusCode, $httpResponseArray;
    public function __construct()
    {
        $this->statusCode = 400;
        $this->httpResponseArray = [];
        $this->middleware('auth:api')->except(['handleAppsMobileCallBack','handleZeepayPaymentCallBack']);
        $this->requestTraceId = Uuid::uuid4()->toString();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $apiUser = JWTAuth::parseToken()->toUser();
            Log::notice("[API][TransactionsController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t called...");
            list($this->httpResponseArray, $this->statusCode) = [[
                "code" => 200, 'trace_id' => $this->requestTraceId, "message" => "paginated transactions list",
                "data" => Transaction::latest()->with(['user','beneficiary'])->paginate(25),],200];

        } catch (Exception $e) {
            Log::error("[API][TransactionsController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][TransactionsController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }

        Log::info("[API][TransactionsController][index][" . $apiUser->id . "]"
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
    public function getUserTransactions(User  $user)
    {
        try {
            $apiUser = JWTAuth::parseToken()->toUser();
            Log::notice("[API][TransactionsController][getUserTransactions][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t called...");

            $transactions = Transaction::where('user_id',$user->id);

            if ($transactions->count() > 0){
                list($this->httpResponseArray, $this->statusCode) = [[
                    "code" => 200, 'trace_id' => $this->requestTraceId, "message" => "paginated transactions list for "
                        .$user->fullName, "data" => $transactions->with(['user','beneficiary'])->latest()
                        ->paginate(25),],200];

            }else{
                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                    "message" => "no transactions found for user ", "data" => []],400];
            }

        } catch (Exception $e) {
            Log::error("[API][TransactionsController][getUserTransactions][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][TransactionsController][getUserTransactions][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }

        Log::info("[API][TransactionsController][getUserTransactions][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param User $user
     * @param Beneficiary $beneficiary
     * @param Rate $rate
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function store(User  $user, Beneficiary  $beneficiary, Rate  $rate, Request $request)
    {
        try {

            $requestContent = str_replace("   ","",
                str_replace("\n", "", $request->getContent())
            );

            $apiUser = JWTAuth::parseToken()->toUser();
            Log::notice("[API][TransactionsController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t called...\t".$requestContent);


            Log::notice("[API][TransactionsController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Rate...\t",$rate->toArray());

            Log::notice("[API][TransactionsController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t User...\t",$user->toArray());

            Log::notice("[API][TransactionsController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Beneficiary...\t",$beneficiary->toArray());


            $requestPayload = json_decode($request->getContent(),true);

            if (!empty($requestPayload) && is_array($requestPayload)){


                if (!(
                    array_key_exists('type',$requestPayload) && !empty($requestPayload['type']) &&
                    array_key_exists('reference',$requestPayload) && !empty($requestPayload['reference']) &&
                    array_key_exists('source_amount',$requestPayload) && !empty($requestPayload['source_amount'])
                    && array_key_exists('source_country',$requestPayload)
                    && !empty($requestPayload['source_country']) &&
                    array_key_exists('destination_amount',$requestPayload) &&
                    !empty($requestPayload['destination_amount']) &&
                    array_key_exists('destination_country',$requestPayload)
                    && !empty($requestPayload['destination_country'])
                )){

                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "invalid request body", "data" => []],400];

                }

                $sourceCountry = UtilitiesController::resolveCountry(trim($requestPayload['source_country']));
                $destinationCountry = UtilitiesController::resolveCountry(trim($requestPayload['destination_country']));


                if (empty($sourceCountry) || empty($destinationCountry)){
                    Log::error("[API][TransactionsController][store][" . $apiUser->id . "]"
                        ."[" . $apiUser->email . "][".$this->requestTraceId."]\t invalid country for destination"
                        ." or send countries ...\t" .trim($requestPayload['source_country'])."\t"
                        .trim($requestPayload['destination_country']));

                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "invalid destination or send country id",
                        "data" => []],400];
                }


                switch ($requestPayload['type']){
                    case "Airtime":
                        if (!array_key_exists('airtimeMSISDN',$requestPayload) &&
                            !empty($requestPayload['airtimeMSISDN'])){
                            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                                'trace_id' => $this->requestTraceId, "message" => "missing required airtimeMSISDN",
                                "data" => []],400];
                        }
                        break;
                }

                // no error message has been assigned above
                if (empty($this->httpResponseArray)){

                    if (!(array_key_exists('purpose',$requestPayload) && !empty($requestPayload['purpose'])))
                    {
                        $requestPayload['purpose'] = "SYSTEM DEFAULT PURPOSE";
                    }

                    $transaction = \App\Transaction::create([
                        'type' => $requestPayload['type'],'source_currency' => $sourceCountry->currency_code,
                        'destination_currency' => $destinationCountry->currency_code,'rate_id' => $rate->id,
                        'source_amount' => $requestPayload['source_amount'],'beneficiary_id' => $beneficiary->id,
                        'destination_amount' => $requestPayload['destination_amount'],'user_id' => $user->id,
                        'purpose' => $requestPayload['purpose']
                    ]);

                    $transaction->reference = $requestPayload['reference'];


                    if ($requestPayload['type']){
                        $transaction->airtimeMsisdn = $request->input('airtimeMSISDN');
                        $transaction->airtimeReceiverName = $beneficiary->firstname;
                        $transaction->airtimeReceiverCountry = $destinationCountry->iso_3166_3;
                    }

                    $transaction->save();

                    list($this->httpResponseArray, $this->statusCode) = [["code" => 201,
                        'trace_id' => $this->requestTraceId, "message" => "transaction record accepted",
                        "data" => ["transaction" => $transaction]],200];

                    //TODO handle star pay to star pay differently
                    dispatch(new \App\Jobs\TransactionsDispatcher($transaction));
                }

            }else{
                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                    'trace_id' => $this->requestTraceId, "message" => "invalid request body", "data" => []],400];
            }
        } catch (Exception $e) {
            Log::error("[API][TransactionsController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][TransactionsController][store][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }


        Log::info("[API][TransactionsController][store][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Transaction  $transaction
     * @return JsonResponse
     */
    public function show(Transaction $transaction)
    {
        $apiUser = JWTAuth::parseToken()->toUser();
        try
        {

            Log::info("[API][TransactionsController][show][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t transaction...",$transaction->load(['user','beneficiary'])
                ->toArray());

            list($this->httpResponseArray, $this->statusCode) =
                [["code" => 200, "message" => "Transaction found", "data" => ['transaction' => $transaction]],200];
        } catch (Exception $e) {
            Log::error("[API][TransactionsController][show][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][TransactionsController][show][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) =
                [["code" => 400, "message" => "application error", "data" => []],400];
        }

        Log::info("[API][TransactionsController][show][" . $apiUser->id . "][" . $apiUser->email . "]"
            ."[" . $this->requestTraceId . "]\t final http response ...\t status: "
            . $this->statusCode . "\t", $this->httpResponseArray);
        return response()->json($this->httpResponseArray,$this->statusCode);
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

    public function handleZeepayPaymentCallBack(Transaction $transaction, Request $request){
        Zeepay::handleTransactionCallBack($transaction,$request);
    }

    public function handleAppsMobileCallBack(Transaction $transaction,Request  $request){
        AppsMobile::handleTransactionCallback($transaction,$request);
    }
}
