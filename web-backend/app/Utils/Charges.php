<?php


namespace App\Utils;


use Illuminate\Support\Facades\Log;

class Charges
{

    public static function resolveCharge($service_name, $account_type, $source_country = null,
                                         $destination_country = null){

        $charge = \App\Charge::whereServiceName($service_name)->whereAccountType($account_type)->latest();

        Log::info("".$charge->toSql()."\t count: ".$charge->count());

        if ($charge->count() <= 0){
            Log::info("[Utils][Charge][resolveCharge]\t... No Charge Exists for service-name: "
                .$service_name."\t account-type: ".$account_type);
            return null;
        }

        $destinationCountryScopedCharge = $charge;

        if(!empty($destination_country)){
            Log::info("[Utils][Charge][resolveCharge]\t... adding destination country limitation ");
            $destinationCountryScopedCharge->whereDestinationCountry($destination_country);

            Log::info("[Utils][Charge][resolveCharge]\t...query: ".$destinationCountryScopedCharge->toSql()
                ."\tcount: ".$destinationCountryScopedCharge->count());
        }

        //if no charge is provided for the service-name + account type ...
        // set the charge back to the service-name + account type only

        if ($destinationCountryScopedCharge->count() <= 0){
            $destinationCountryScopedCharge = $charge;
        }

        Log::info("[Utils][Charge][resolveCharge]\t...destination scoped query : ".
            $destinationCountryScopedCharge->toSql() ."\tcount: ".$destinationCountryScopedCharge->count());

        $sourceCountryScopedCharge = $destinationCountryScopedCharge;

        if(!empty($source_country)){
            Log::info("[Utils][Charge][resolveCharge]\t... adding source country limitation ");
            $sourceCountryScopedCharge->whereSourceCountry($source_country);
            Log::info("[Utils][Charge][resolveCharge]\t...query: ".$sourceCountryScopedCharge->toSql()
                ."\tcount: ".$sourceCountryScopedCharge->count());
        }

        Log::info("[Utils][Charge][resolveCharge]\t...destination scoped query :\tcount: ".
            $destinationCountryScopedCharge->count());

        Log::info("[Utils][Charge][resolveCharge]\t...source scoped query :\tcount: ".
            $sourceCountryScopedCharge->count());


        if ($sourceCountryScopedCharge->count() > 0){
            $sourceCountryScopedCharge = $sourceCountryScopedCharge->first();
            Log::info("[Utils][Charge][resolveCharge]\t...returning : ", $sourceCountryScopedCharge->toArray());
            return $sourceCountryScopedCharge;
        }elseif ($destinationCountryScopedCharge->count() > 0){
            $destinationCountryScopedCharge = $destinationCountryScopedCharge->first();
            Log::info("[Utils][Charge][resolveCharge]\t...returning : ",
                $destinationCountryScopedCharge->toArray());
            return $destinationCountryScopedCharge;
        }

        return null;
    }
}