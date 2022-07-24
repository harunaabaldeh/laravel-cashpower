<?php


namespace App\Utils;


class Transaction
{

    public static function updateTransactionStatus(\App\Transaction  $transaction, $status, $status_message = null){

        if ($status == "Success"){
            $transaction->status = "Success";
            $transaction->status_message =  empty($status_message) ? "Transaction Processed Successfully"
                : $status_message;
        }


        if ($status == "Error"){
            $transaction->status = "Error";
            $transaction->status_message =  empty($status_message) ? "Transaction Declined"
                : $status_message;
        }


        $transaction->status = $status;
        $transaction->status_message =  empty($status_message) ? "Pending Gateway Response"
            : $status_message;
        $transaction->save();
    }


}