<?php

namespace App\Console\Commands;

use App\Http\Controllers\RatesController;
use Illuminate\Console\Command;

class UpdateSystemRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rates:update  {date=None} {sourceCurrency=None} {destinationCurrency=None}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try
        {

            $rateDate = $this->argument('date');
            $sourceCurrency = $this->argument('sourceCurrency');
            $destinationCurrency = $this->argument('destinationCurrency');

            $sourceCurrencies = array_unique(\App\RateSetting::pluck('source_currency')->toArray());

            foreach ($sourceCurrencies as $key => $currency){
                RatesController::setSystemBaseRates($currency);
            }

        }catch (\Exception $exception)
        {

        }
    }
}
