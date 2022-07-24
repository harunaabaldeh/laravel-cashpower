<?php

namespace App\Jobs;

use App\Http\Controllers\UtilitiesController;
use App\Transaction;
use App\Utils\AppsMobile;
use App\Utils\GamSwitch;
use App\Utils\Zeepay;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TransactionsDispatcher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private  $transaction;
    /**
     * Create a new job instance.
     *
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try{

            Log::info("[TransactionsDispatcher][".$this->transaction->id."]\t called");
            $user = $this->transaction->user;
            $beneficiary = \App\Beneficiary::find($this->transaction->beneficiary_id);
            if ($user->balance >= $this->transaction->source_amount)
            {
                Log::info("[TransactionsDispatcher][".$this->transaction->id."]\t user has enough balance...".
                    " proceeding to debit wallet");
                UtilitiesController::debitUserWallet($user,$this->transaction);
                Log::info("[TransactionsDispatcher][".$this->transaction->id."]\t Done with Debit..".
                    " proceeding to dispatch transfer");


                switch ($this->transaction->type){
                    case "star-pay":
                        Log::info("[TransactionsDispatcher][".$this->transaction->id."]\t".
                            " Doing cartis pay transfer");

                        $message = "Hello ".$beneficiary->firstname.", You have received a cartis pay Transfer from # "
                            . $user->star_account_number." of ". $this->transaction->destination_currency.' '.
                            $this->transaction->destination_amount;

                        $beneficiaryUser = \App\User::where('star_account_number',
                            $beneficiary->account_number)->first();

                        UtilitiesController::creditUserWallet($beneficiaryUser,$this->transaction->destination_amount,
                        $message,$this->transaction->id);
                        \App\Utils\Transaction::updateTransactionStatus($this->transaction, "Success",
                            "Transaction Processed Successfully");

                        break;
                    case "Airtime":
                        Log::info("[TransactionsDispatcher][".$this->transaction->id."]\t".
                            " airtime transfer");
                        if ($this->transaction->destination_currency == "GMD"){
                            GamSwitch::doAirtimeTopUp($this->transaction);
                        }elseif ($this->transaction->destination_currency == "GHS"){
                            AppsMobile::dispatchAirtime($this->transaction);
                        }else{
                            //TODO DT One Integration
                        }
                        break;
                    case "bill":
                        Log::info("[TransactionsDispatcher][".$this->transaction->id."]\t".
                            " bill pay transaction");
                        if ($this->transaction->destination_currency == "GMD"){
//                            GamSwitch::doElectricityTopUp($this->transaction);
                        }elseif ($this->transaction->destination_currency == "GHS"){
                            Zeepay::completeBillPayment($this->transaction);
                        }else{
                            //TODO DT One Integration
                        }
                        break;
                    case "Wallet":
                        Log::info("[TransactionsDispatcher][".$this->transaction->id."]\t".
                            " wallet transfer");
                        if ($this->transaction->destination_currency == "GHS"){
                            AppsMobile::dispatchMobileMoney($this->transaction);
                        }else{
                            //TODO DT One Integration
                        }
                        break;
                    default:
                        Log::info("[TransactionsDispatcher][".$this->transaction->id."]\t".
                            " can't route.. failing transaction");
                        $this->transaction->status = "Error";
                        $this->transaction->status_message =  "Routing Not Supported";
                        $this->transaction->save();
                        return;
                }

            }else
            {
                Log::info("[TransactionsDispatcher][".$this->transaction->id."]\t".
                    " low funds...");
                $this->transaction->status = "Error";
                $this->transaction->status_message =  "Insufficient Account Balance";
                $this->transaction->save();
            }

        }catch (\Exception $exception){
            Log::error("[TransactionsDispatcher][".$this->transaction->id."]\t... Exception..\t"
                .$exception->getMessage());
            Log::error("[TransactionsDispatcher][".$this->transaction->id."]\t... Exception..\t"
                .$exception->getTraceAsString());
        }
    }
}
