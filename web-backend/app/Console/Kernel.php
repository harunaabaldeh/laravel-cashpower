<?php

namespace App\Console;

use App\Console\Commands\UpdateSystemRates;
use App\Http\Controllers\RatesController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        UpdateSystemRates::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->call(function (){
            $sourceCurrencies = array_unique(\App\RateSetting::pluck('source_currency')->toArray());

            foreach ($sourceCurrencies as $key => $currency){
                RatesController::setSystemBaseRates($currency);
            }

        })->dailyAt("03:00");

        $schedule->command('accounts:bill-monthly-fees')->monthlyOn(1,"01:15")
            ->environments(["production"]);

/*        $schedule->call(function (){
            $date = \Carbon\Carbon::now();
            $file_name = "system-rates-".$date->format("Y-m-d").".csv";
            Storage::disk('local')->put($file_name, "Created Date, Id, Source Currency, Destination Currency, Rate");
            \App\Rate::whereDate('created_at',$date->format('Y-m-d'))->chunk(1000,function ($rates) use ($file_name){
                foreach ($rates as $rate){
                    Storage::disk('local')->append($file_name,$rate->created_at.",".$rate->id.",".$rate->source_currency.",".$rate->destination_currency.",".$rate->rate);
                }
            });


            Mail::raw("Daily Rate Statement Dump ", function ($message) use ($file_name) {
                $message->to("kojo@starpayonline.com")
                    ->cc("kojoamie@hotmail.com")->
                    attach(storage_path("app/".$file_name))->subject("Daily Rate Statement Report - ".\Carbon\Carbon::now()->subDay()->format("Y-m-d"));
            });

        })->dailyAt("05:00")->environments(["production"]);*/

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
