<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class RateSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = ['GMD','GHS','NGN','XAF','UGX','XOF','USD','GBP','EUR','CHF','SEK','NOK','DKK','CAD'];

        foreach ($currencies as $currency) {
            $destinationCurrencies = Arr::except($currencies,$currency);
            foreach ($destinationCurrencies as $destinationCurrency) {
                $rateSetting = \App\RateSetting::where(['source_currency' => $currency,'destination_currency' => $destinationCurrency,])->first();
                if (empty($rateSetting)){
                    \App\RateSetting::create(['source_currency' => $currency,'destination_currency' => $destinationCurrency,]);
                }
                $rate = \App\Rate::where(['source_currency' => $currency,'destination_currency' => $destinationCurrency,])->first();
                if (empty($rate)){
                    \App\Rate::create(['source_currency' => $currency,'destination_currency' => $destinationCurrency,]);
                }
            }
        }
    }
}
