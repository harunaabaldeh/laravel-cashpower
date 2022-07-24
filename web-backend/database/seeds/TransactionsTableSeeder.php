<?php

use Illuminate\Database\Seeder;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\User::find([1,2]);

        foreach ($users as $user){

            for ($i = 0; $i < 45; $i++){


                $type = \Arr::random(["Airtime","Wallet","Bank","Pickup"]);
                $status = \Arr::random(["Pending","Error","Cancelled","Success"]);

                if ($status == "Pending"){
                    $status_message = \Arr::random(["Pending Validation","Pending Gateway Response",]);
                }

                if ($status == "Error"){
                    $status_message = \Arr::random(["Unregistered Wallet.","Customer Wallet Limit Breach","Unregistered Wallet","Failed At Payment Gateway","Customer Wallet/Name Mismatch","Invalid/Missing MSISDN","Routing Details Unknown",]);
                }

                if ($status == "Cancelled"){
                    $status_message = \Arr::random(["Cancelled By Originating Partner","Payment Rejected"]);
                }

                if ($status == "Success"){
                    $status_message = "Transaction Processed Successfully";
                }


                $beneficiary = \App\Beneficiary::where('user_id',$user->id)->inRandomOrder()->first();

                $rate = \App\Rate::where(['source_currency' => $user->country->currency_code, 'destination_currency' => $beneficiary->country->currency_code])->inRandomOrder()->first();

                if (!empty($rate)){
                    $amount = mt_rand(10,73);
                    $receiveAmount = round(($amount * $rate->rate),2,PHP_ROUND_HALF_DOWN);
                    \App\Transaction::create(['type' => $type,"status" => $status,"status_message" => $status_message,"source_currency" => $user->country->currency_code,"destination_currency" => $beneficiary->country->currency_code,
                        "source_amount" => $amount,"destination_amount" => $receiveAmount,"user_id" => $user->id,"rate_id" => $rate->id,"beneficiary_id" => $beneficiary->id,]);

                }

            }

        }
    }
}
