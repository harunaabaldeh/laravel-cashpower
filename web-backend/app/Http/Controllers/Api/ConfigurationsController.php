<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilitiesController;
use App\RateSetting;
use App\ServiceTypeCountryConfiguration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Tymon\JWTAuth\Facades\JWTAuth;

class ConfigurationsController extends Controller
{
    private  $requestTraceId, $statusCode, $httpResponseArray;
    public function __construct()
    {
        $this->statusCode = 400;
        $this->httpResponseArray = [];
        $this->middleware('auth:api');
        $this->requestTraceId = Uuid::uuid4()->toString();

    }


    public function getSupportedCountries(){
        $apiUser = JWTAuth::parseToken()->toUser();
        try {
            Log::info("[API][ConfigurationsController][getSupportedCountries][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t called ...\t");

            $countries =
                \App\Country::get(['id', 'currency_code', 'currency_symbol', 'iso_3166_2', 'iso_3166_3', 'name']);


            list($this->httpResponseArray, $this->statusCode) =
                [
                    [
                        "code" => 200, 'trace_id' => $this->requestTraceId,
                        "message" => "supported countries configuration found", "data" => $countries
                    ],
                    200
                ];


            /*            $countryIds = ServiceTypeCountryConfiguration::pluck('countries')->toArray();
                        $countryIds = Arr::flatten(array_unique(Arr::collapse($countryIds)));
                        $countries = \Countries::whereIn('id',$countryIds);


                        if($countries->count() > 0){

                            $countries =
                                $countries->get(['id', 'currency_code', 'currency_symbol', 'iso_3166_2', 'iso_3166_3', 'name']);


                            list($this->httpResponseArray, $this->statusCode) =
                                [
                                    [
                                        "code" => 200, 'trace_id' => $this->requestTraceId,
                                        "message" => "supported countries configuration found", "data" => $countries
                                    ],
                                200
                                ];

                        }else{
                            list($this->httpResponseArray, $this->statusCode) =
                                [
                                    [
                                        "code" => 400,
                                        'trace_id' => $this->requestTraceId,
                                        "message" => "no country configurations found",
                                        "data" => null
                                    ],
                                400
                            ];
                        }*/

        } catch (\Exception $e) {
            Log::info("[API][ConfigurationsController][getSupportedCountries][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getMessage());
            Log::info("[API][ConfigurationsController][getSupportedCountries][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getTraceAsString());
            list($this->httpResponseArray, $this->statusCode) =
                [
                    [
                        "code" => 400,
                        'trace_id' => $this->requestTraceId,
                        "message" => "application error",
                        "data" => null
                    ],
                    400];
        }

        Log::info("[API][ConfigurationsController][getSupportedCountries][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }


    public function getSupportedServices(){
        $apiUser = JWTAuth::parseToken()->toUser();
        try {
            Log::info("[API][ConfigurationsController][getSupportedServices][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t called ...\t");


            if(ServiceTypeCountryConfiguration::count() > 0){

                list($this->httpResponseArray, $this->statusCode) =
                    [
                        [
                            "code" => 200, 'trace_id' => $this->requestTraceId,
                            "message" => "supported countries configuration found", "data" =>
                            ServiceTypeCountryConfiguration::pluck('service_name')
                        ],
                    200
                    ];

            }else{
                list($this->httpResponseArray, $this->statusCode) =
                    [
                        [
                            "code" => 400,
                            'trace_id' => $this->requestTraceId,
                            "message" => "no services found",
                            "data" => null
                        ],
                    400
                ];
            }

        } catch (\Exception $e) {
            Log::info("[API][ConfigurationsController][getSupportedServices][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getMessage());
            Log::info("[API][ConfigurationsController][getSupportedServices][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getTraceAsString());
            list($this->httpResponseArray, $this->statusCode) =
                [
                    [
                        "code" => 400,
                        'trace_id' => $this->requestTraceId,
                        "message" => "application error",
                        "data" => null
                    ],
                    400];
        }

        Log::info("[API][ConfigurationsController][getSupportedServices][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }


    public function getSupportedServicesByCountry($countryId){
        $apiUser = JWTAuth::parseToken()->toUser();
        try {

            Log::info("[API][ConfigurationsController][getSupportedServicesByCountry][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t called ...country-id: ".$countryId);

            $country = UtilitiesController::resolveCountry($countryId);

            if (empty($country)){
                list($this->httpResponseArray, $this->statusCode) =
                    [
                        [
                            "code" => 400,
                            'trace_id' => $this->requestTraceId,
                            "message" => "invalid country code",
                            "data" => null
                        ],
                        400
                    ];
            }


            $services = ServiceTypeCountryConfiguration::whereJsonContains('countries',$country->id);

            if($services->count() > 0){

                list($this->httpResponseArray, $this->statusCode) =
                    [
                        [
                            "code" => 200, 'trace_id' => $this->requestTraceId,
                            "message" => "supported services for ".$country->iso_3166_3,
                            "data" => $services->pluck('service_name')
                        ],
                    200
                    ];

            }else{

                //default to star-pay service

                list($this->httpResponseArray, $this->statusCode) =
                    [
                        [
                            "code" => 200, 'trace_id' => $this->requestTraceId,
                            "message" => "supported services for ".$country->iso_3166_3,
                            "data" => ['starpay-2-starpay']
                        ],
                        200
                    ];



/*                list($this->httpResponseArray, $this->statusCode) =
                    [
                        [
                            "code" => 400,
                            'trace_id' => $this->requestTraceId,
                            "message" => "no services found for ".$country->iso_3166_3,
                            "data" => null
                        ],
                    400
                ];*/
            }

        } catch (\Exception $e) {
            Log::info("[API][ConfigurationsController][getSupportedServicesByCountry][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getMessage());
            Log::info("[API][ConfigurationsController][getSupportedServicesByCountry][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getTraceAsString());
            list($this->httpResponseArray, $this->statusCode) =
                [
                    [
                        "code" => 400,
                        'trace_id' => $this->requestTraceId,
                        "message" => "application error",
                        "data" => null
                    ],
                    400];
        }

        Log::info("[API][ConfigurationsController][getSupportedServicesByCountry][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }


    public function getSupportCountriesByServiceName($serviceName){
        $apiUser = JWTAuth::parseToken()->toUser();
        try {
            Log::info("[API][ConfigurationsController][getSupportCountriesByServiceName][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t called ...service-name: ".$serviceName);


            $services = ServiceTypeCountryConfiguration::where('service_name',$serviceName);

            if($services->count() > 0){

                $countryIds = $services->pluck('countries')->toArray();
                $countryIds = Arr::flatten(array_unique(Arr::collapse($countryIds)));
                $countries = \Countries::whereIn('id',$countryIds);

                $countries =
                    $countries->get(['id', 'currency_code', 'currency_symbol', 'iso_3166_2', 'iso_3166_3', 'name']);


                list($this->httpResponseArray, $this->statusCode) =
                    [
                        [
                            "code" => 200, 'trace_id' => $this->requestTraceId,
                            "message" => "supported countries for  service: ".$serviceName,
                            "data" => $countries
                        ],
                    200
                    ];

            }else{
                list($this->httpResponseArray, $this->statusCode) =
                    [
                        [
                            "code" => 400,
                            'trace_id' => $this->requestTraceId,
                            "message" => "no countries found for service: ".$serviceName,
                            "data" => null
                        ],
                    400
                ];
            }

        } catch (\Exception $e) {
            Log::info("[API][ConfigurationsController][getSupportCountriesByServiceName][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getMessage());
            Log::info("[API][ConfigurationsController][getSupportCountriesByServiceName][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getTraceAsString());
            list($this->httpResponseArray, $this->statusCode) =
                [
                    [
                        "code" => 400,
                        'trace_id' => $this->requestTraceId,
                        "message" => "application error",
                        "data" => null
                    ],
                    400];
        }

        Log::info("[API][ConfigurationsController][getSupportCountriesByServiceName][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    public function getRate($sourceCurrency,$destinationCurrency){
        $apiUser = JWTAuth::parseToken()->toUser();
        try
        {
            Log::info("[API][ConfigurationsController][getRate][" . $apiUser->id . "][" . $apiUser->email . "]"
                . "[" . $this->requestTraceId . "]\t called ...\t");

            $rate = \App\Rate::where([
                'source_currency' => $sourceCurrency,
                'destination_currency' => $destinationCurrency
            ])->latest()->first();


            if (!empty($rate)) {
                $rate = Arr::except($rate, ['created_at', 'properties', 'updated_at', 'deleted_at']);

                list($this->httpResponseArray, $this->statusCode) = [["code" => 200, 'trace_id' =>
                    $this->requestTraceId, "message" => "rate found for currency combination", "data" => $rate],200];
            }else{

                if(str)
                list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' =>
                    $this->requestTraceId, "message" => "no rates found for given combination", "data" => null], 400];
            }


        } catch (\Exception $e) {
            Log::info("[API][ConfigurationsController][getRate][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getMessage());
            Log::info("[API][ConfigurationsController][getRate][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getTraceAsString());
            list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => null],400];
        }


        Log::info("[API][ConfigurationsController][getRate][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    public function getLatestRates(){
        $apiUser = JWTAuth::parseToken()->toUser();
        try
        {
            Log::info("[API][ConfigurationsController][getLatestRates][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t called ...\t");

            $latestRate = \App\Rate::latest()->first();


            if (!empty($latestRate)) {
                $created_at = Carbon::parse($latestRate->created_at);
                $rates = \App\Rate::whereDate('created_at',$created_at->format('Y-m-d'))->latest()->get([
                    'id','source_currency','destination_currency','rate'
                ]);
                if ($rates->count()){
                    list($this->httpResponseArray, $this->statusCode) = [["code" => 200, 'trace_id' =>
                        $this->requestTraceId, "message" => "latest rate configurations found", "data" => $rates]
                        ,200];
                }
            }


            if (empty($this->httpResponseArray)){
                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                    'trace_id' => $this->requestTraceId, "message" => "no rates found", "data" => null], 400];
            }


        } catch (\Exception $e) {
            Log::info("[API][ConfigurationsController][getLatestRates][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getMessage());
            Log::info("[API][ConfigurationsController][getLatestRates][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getTraceAsString());
            list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => null],400];
        }

        Log::info("[API][ConfigurationsController][getLatestRates][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }


    public function getRatesByDate($date){
        $apiUser = JWTAuth::parseToken()->toUser();
        try
        {
            $parsedDate = null;


            try{
                $parsedDate = Carbon::parse($date);
            }catch (\Exception $exception){
                Log::info("[API][ConfigurationsController][getRatesByDate][" . $apiUser->id . "]"
                    ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Date Parse Exception ...\t"
                    .$exception->getMessage());
            }


            if (!empty($parsedDate)){
                Log::info("[API][ConfigurationsController][getRatesByDate][" . $apiUser->id . "]"
                    ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t called ...\t");


                $rates = \App\Rate::whereDate('created_at',$parsedDate->format('Y-m-d'))->latest()->get([
                    'id','source_currency','destination_currency','rate'
                ]);

                if ($rates->count()){
                    list($this->httpResponseArray, $this->statusCode) = [["code" => 200, 'trace_id' =>
                        $this->requestTraceId, "message" => "system rates for ".$parsedDate->format("Y-m-d"),
                        "data" => $rates],200];
                }


                if (empty($this->httpResponseArray)){
                    list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                        'trace_id' => $this->requestTraceId, "message" => "no rates found", "data" => null], 400];
                }
            }else{
                list($this->httpResponseArray, $this->statusCode) = [["code" => 400,
                    'trace_id' => $this->requestTraceId, "message" => "invalid/missing date", "data" => null], 400];
            }


        } catch (\Exception $e) {
            Log::info("[API][ConfigurationsController][getRatesByDate][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getMessage());
            Log::info("[API][ConfigurationsController][getRatesByDate][" . $apiUser->id . "]"
                ."[" . $apiUser->email . "][".$this->requestTraceId."]\t Exception ...\t".$e->getTraceAsString());
            list($this->httpResponseArray, $this->statusCode) = [["code" => 400, 'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => null],400];
        }

        Log::info("[API][ConfigurationsController][getRatesByDate][" . $apiUser->id . "]"
            ."[" . $apiUser->email . "][" . $this->requestTraceId . "]\t final http response ...\t status: "
            .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }
}
