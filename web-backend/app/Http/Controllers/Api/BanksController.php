<?php

namespace App\Http\Controllers\Api;

use App\Bank;
use App\Beneficiary;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilitiesController;
use App\ServiceTypeCountryConfiguration;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Tymon\JWTAuth\Facades\JWTAuth;

class BanksController extends Controller
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
            Log::notice("[API][BanksController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t called...");
            list($this->httpResponseArray, $this->statusCode) = [[
                "code" => 200, 'trace_id' => $this->requestTraceId, "message" => "paginated banks list",
                "data" => Bank::latest()->paginate(25),],200];

        } catch (Exception $e) {
            Log::error("[API][BanksController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getMessage());
            Log::error("[API][BanksController][index][" . $apiUser->id . "][" . $apiUser->email . "]"
                ."[".$this->requestTraceId."]\t Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }

        Log::info("[API][BanksController][index][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    public function getSupportedServicesByCountry($countryId){
        $apiUser = JWTAuth::parseToken()->toUser();
        try {

            Log::info("[API][BanksController][getSupportedServicesByCountry][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t called ...country-id: ".$countryId);

            $country = UtilitiesController::resolveCountry($countryId);

            if (empty($country)){
                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                    'trace_id' => $this->requestTraceId, "message" => "invalid country code", "data" => null], 400];
            }


            $banks = Bank::where('country_code',$country->iso_3166_3);

            if($banks->count() > 0){
                list($this->httpResponseArray, $this->statusCode) = [["code" => 200,
                    'trace_id' => $this->requestTraceId, "message" => "banks list for ".$country->iso_3166_3,
                    "data" => $banks->get()], 200];

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
            Log::info("[API][BanksController][getSupportedServicesByCountry][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getMessage());
            Log::info("[API][BanksController][getSupportedServicesByCountry][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getTraceAsString());
            list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => null], 400];
        }

        Log::info("[API][BanksController][getSupportedServicesByCountry][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

}
