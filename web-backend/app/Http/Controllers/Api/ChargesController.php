<?php

namespace App\Http\Controllers\Api;

use App\Charge;




use Exception;
use Ramsey\Uuid\Uuid;
use App\Utils\Charges;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\UtilitiesController;




class ChargesController extends Controller
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
//            ->get(['account_type','id','service_name','fixed_charge', 'percentage_charge','source_country',
//'destination_country'])

            $apiUser = JWTAuth::parseToken()->toUser();
            Log::notice("[API][ChargesController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t called...");
            list($this->httpResponseArray, $this->statusCode) = [[
                    "code" => 200, 'trace_id' => $this->requestTraceId, "message" => "paginated charge list",
                    "data" => Charge::latest()->paginate(25),],200];

        } catch (Exception $e) {
            Log::error("[API][ChargesController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][ChargesController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                    "message" => "application error", "data" => []],400];
        }

        Log::info("[API][ChargesController][index][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    /**
     * check if a user exists for a given
     * msisdn/password pair
     * @param Request $request
     * @return JsonResponse
     */
    public function resolveCharge(Request  $request)
    {

        $apiUser = JWTAuth::parseToken()->toUser();
        try
        {

            $requestContent = str_replace("   ","",
                str_replace("\n", "", $request->getContent())
            );

            Log::info("[API][ChargesController][resolveCharge][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[" . $this->requestTraceId . "]\t called ...\t ".Crypt::encrypt($requestContent));


            $requestPayload = json_decode($request->getContent(),true);

            if (!empty($requestPayload) && is_array($requestPayload)){

                if (
                    array_key_exists('account_type',$requestPayload) && !empty($requestPayload['account_type']) &&
                    array_key_exists('service_name',$requestPayload) && !empty($requestPayload['service_name'])
                ){

                    $sourceCountry = $destinationCountry =  null;

                    if (array_key_exists('source_country',$requestPayload) &&
                        !empty($requestPayload['source_country'])){
                        $sourceCountry = UtilitiesController::resolveCountry($requestPayload['source_country']);
                    }

                    if (array_key_exists('destination_country',$requestPayload) &&
                        !empty($requestPayload['destination_country'])){
                        $destinationCountry = UtilitiesController::resolveCountry(
                            $requestPayload['destination_country']);
                    }



                    $source_country = !empty($sourceCountry) ? $sourceCountry->iso_3166_3 : null;
                    $destinationCountry = !empty($destinationCountry) ? $destinationCountry->iso_3166_3 : null;


                    $charge = \App\Utils\Charges::resolveCharge($requestPayload['service_name'],
                        $requestPayload['account_type'], $source_country,$destinationCountry);

                    if (empty($charge)){
                        //TODO revert to charge failures if a charge is not found
                     /*   list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                            'trace_id' => $this->requestTraceId, "message" => "no charge configurations found",
                            "data" => null],400];*/

  /*                      $charge = new \App\Charge();
                        $charge->account_type = $requestPayload['account_type'];
                        $charge->service_name = $requestPayload['service_name'];
                        $charge->fixed_charge = 0.00;
                        $charge->percentage_charge = 1.00;
                        $charge->source_country = null;
                        $charge->destination_country = null;*/

                        //TODO more this to ChargeUtil...
                        $charge = \App\Charge::where('account_type','default')->first();
                        list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                            'trace_id' => $this->requestTraceId, "message" => "default charge configuration used",
                            "data" => $charge],200];
                    }else{

                        list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                            'trace_id' => $this->requestTraceId, "message" => "charges configurations found",
                            "data" => $charge],200];
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
            Log::error("[API][ChargesController][resolveCharge][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][ChargesController][resolveCharge][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => null],400];
        }

        Log::info("[API][ChargesController][resolveCharge][" . $apiUser->id . "][" . $apiUser->email . "]"
            ."[" . $this->requestTraceId . "]\t final http response ...\t status: "
            . $this->statusCode . "\t", $this->httpResponseArray);
        return response()->json($this->httpResponseArray,$this->statusCode);
    }

}
