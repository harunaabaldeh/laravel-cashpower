<?php

namespace App\Observers;

use App\Transaction;
use App\Utils\BroadcastUtil;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function created(Transaction $transaction)
    {
        try {
            Log::info("[TransactionObserver][created][".$transaction->id."]\t ", $transaction->toArray());
            self::broadcastTransactionActivity($transaction,"transaction-created");
        }catch (\Exception $exception)
        {
            Log::error("[TransactionObserver][created][".$transaction->id."]\t Exception: ".
                $exception->getMessage());
        }
    }

    /**
     * Handle the Transaction "updated" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function updated(Transaction $transaction)
    {
        try {
            Log::info("[TransactionObserver][updated][".$transaction->id."]\t ", $transaction->toArray());
            self::broadcastTransactionActivity($transaction,"transaction-updated");
        }catch (\Exception $exception)
        {
            Log::error("[TransactionObserver][updated][".$transaction->id."]\t Exception: ".
                $exception->getMessage());
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function deleted(Transaction $transaction)
    {
        try {
            Log::info("[TransactionObserver][deleted][".$transaction->id."]\t ", $transaction->toArray());
            self::broadcastTransactionActivity($transaction,"transaction-deleted");
        }catch (\Exception $exception)
        {
            Log::error("[TransactionObserver][deleted][".$transaction->id."]\t Exception: ".
                $exception->getMessage());
        }
    }

    /**
     * Handle the Transaction "restored" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function restored(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function forceDeleted(Transaction $transaction)
    {
        //
    }


    private static function broadcastTransactionActivity(Transaction  $transaction, $event)
    {
        try {
            $transactionTypeMap = [
                "bill" => "BILL",
                "Wallet" => "WALLET",
                "Airtime" => "AIRTIME",
                "Bank" => "BANK_ACCOUNT",
                "Pickup" => "CASH",
                "star-pay" => "STAR_PAY_TO_STAR_PAY",
                "Card" => "CARD_TRANSFER",
                "Account Topup" => "STARPAY_ACCOUNT",
            ];

            $beneficiary = $transaction->beneficiary;

            $provider = $beneficiary->bank_name;
            $transactionType = $transaction->type;

            if (trim(strtolower($transactionType)) == "bank"){
                $accountNumber = $beneficiary->account_number;
            }elseif (trim(strtolower($transactionType)) == "bill"){

                $accountNumber = $beneficiary->account_number;

                if (Str::contains($accountNumber,"_"))
                {
                    $accountDetails = explode("_",trim($beneficiary->account_number));
                    $accountNumber =  $accountDetails[0];
                }

            }else{
                $accountNumber = $beneficiary->msisdn;
            }

            $transactionType = $transactionTypeMap[trim($transactionType)];
            BroadcastUtil::doBroadCast("user-".$transaction->user->msisdn,$event,['transaction' =>
                $transaction->toArray(), 'recipientName' => $beneficiary->fullName, 'accountNumber' => $accountNumber,
                "transactionType" => $transactionType, "charge" => null, "provider" => $provider]);

        }catch (\Exception $exception){
            Log::error("[UtilitiesController][broadcastEvalueChangeActivity]\t Error doing broadcast\t ".
                $exception->getMessage());
        }
    }
}
