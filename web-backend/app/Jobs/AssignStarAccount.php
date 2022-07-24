<?php

namespace App\Jobs;

use App\Http\Controllers\UtilitiesController;
use App\User;
use App\Utils\BroadcastUtil;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class AssignStarAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;

    /**
     * Create a new job instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{

            $star_account = mt_rand(1000000000,9999999999);

            while (\App\User::where('star_account_number',$star_account)->count() > 0){
                $star_account = mt_rand(1000000000,9999999999);
            }

            $this->user->accountStatus = "active";
            $this->user->star_account_number = $star_account;
            $this->user->save();

            BroadcastUtil::doBroadCast("user-".$this->user->msisdn,
                "cartis-pay-account-assigned",['user' => $this->user->toArray(),
                    'account_number' => $star_account,]
            );

            $template_message = env("WELCOME_MESSAGE_TEMPLATE");
            $template_message = str_replace("@@firstname@@",$this->user->firstname,$template_message);
            $template_message = str_replace("@@msisdn@@",$this->user->msisdn,$template_message);
            $template_message = str_replace("@@account@@",$star_account,$template_message);

            UtilitiesController::sendSMS($template_message,$this->user->msisdn);
        }catch (\Exception $exception){
            Log::error("[AssignSkyAccount]\t Exception: ".$exception->getMessage());
            Log::error("[AssignSkyAccount]\t".$exception->getTraceAsString());
        }
    }
}
