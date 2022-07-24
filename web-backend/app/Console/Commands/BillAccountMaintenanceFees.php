<?php

namespace App\Console\Commands;

use App\Charge;
use App\Country;
use App\Fund;
use App\Http\Controllers\UtilitiesController;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BillAccountMaintenanceFees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:bill-monthly-fees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debit Each user for their monthly maintenance fees';

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
     * @return int
     */
    public function handle()
    {
        $charges = Charge::where('service_name', 'Monthly Charges')->get();
        Log::debug("[BillAccountMaintenanceFees]\t Number of Charges Found: ".$charges->count());
        foreach ($charges as $charge){
            $amount = $charge->fixed_charge;
            $country = UtilitiesController::resolveCountry($charge->destination_country);

            if (!empty($country)){
                $users = User::where('country_id',$country->id)->get();
                Log::debug("[BillAccountMaintenanceFees]\t User Count for country ".
                    $charge->destination_country."\t ".$users->count());
                foreach ($users as $user){
                    if ($user->isAgentUser || $user->isAdminUser){
                        continue;
                    }
                    $balanceBefore = $user->balance;
                    $balanceAfter = round($user->balance - $amount,2, PHP_ROUND_HALF_DOWN);

                    $description = "Monthly Maintenance Fee - " . \Carbon\Carbon::now()->subMonth()
                            ->format("M Y");
                    $fund = Fund::create(['type' => 'debit','amount' => $amount,'balance_before' => $balanceBefore,
                        'balance_after' => $balanceAfter,'description' => $description, 'user_id' => $user->id ]);
                    $user->balance = $balanceAfter;
                    $user->save();

                    $currency_code = $user->country->currency_code;
                    $message = "Action: Account Debit\r\nAmount: ". $currency_code ." ".$amount.
                        "\r\nBalance Before: ".$currency_code." ".$balanceBefore."\r\nBalance After: ".$currency_code.
                        " ".$balanceAfter. "\r\nActivity: ". $description."\r\nTime: ".now()
                            ->format("Y-m-d H:i:s")."\r\nReference: ".$fund->id;
                    UtilitiesController::sendSMS($message,$user->msisdn);
                }
            }
        }
        return Command::SUCCESS;
    }
}
