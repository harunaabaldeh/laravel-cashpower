<?php

use App\Http\Controllers\RatesController;
use Illuminate\Database\Seeder;

class RatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sourceCurrencies = array_unique(\App\RateSetting::pluck('source_currency')->toArray());

        foreach ($sourceCurrencies as $key => $currency){
            RatesController::setSystemBaseRates($currency);
        }
    }
}
