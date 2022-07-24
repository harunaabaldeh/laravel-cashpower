<?php

namespace App\Http\Controllers\Api;

use App\User;
use Exception;
use App\Transaction;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Utils\BroadcastUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class BroadcastController extends Controller
{
    private  $requestTraceId, $statusCode, $httpResponseArray;
    public function __construct()
    {
        $this->statusCode = 400;
        $this->httpResponseArray = [];
        $this->requestTraceId = Uuid::uuid4()->toString();
    }

    /**
     * Fake e-value broadcast event
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function eValueUpdateBroadcast(Request $request): JsonResponse
    {
        try {

            $user = User::where('id',$request->identifier)->orWhere('msisdn',$request->identifier)
                ->orWhere('star_account_number',$request->identifier)->first();

            $amount = $request->amount;
            $balanceBefore = $user->balance;

            if ($request->event == "user-debit"){
                $balanceAfter = $balanceBefore - $amount;
            }

            if ($request->event == "user-credit"){
                $balanceAfter = $amount + $balanceBefore;
            }

            $user->balance = $balanceAfter;
            BroadcastUtil::doBroadCast("user-".$user->msisdn,$request->event,['user' => $user->toArray(),
                'amount' => $request->amount, 'balance_before' => $balanceBefore,'balance_after' => $balanceAfter]);
            list($this->httpResponseArray, $this->statusCode) = [["code" => 200,'trace_id' => $this->requestTraceId,
                "message" => "Broad Cast Complete", "data" => []],200];
        } catch (Exception $e) {
            Log::error("[API][BroadcastController][eValueUpdateBroadcast][".$this->requestTraceId."]\t
             Error...".$e->getMessage());
            Log::error("[API][BroadcastController][eValueUpdateBroadcast][".$this->requestTraceId."]\t 
            Error...".$e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }

        Log::info("[API][BroadcastController][eValueUpdateBroadcast][" . $this->requestTraceId . "]\t 
        final http response ...\t status: " .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

    /**
     * Fake account assigned event
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function accountAssignedBroadcast(Request $request): JsonResponse
    {
        try {

            $user = User::where('id',$request->identifier)->orWhere('msisdn',$request->identifier)
                ->orWhere('star_account_number',$request->identifier)->first();
            
            BroadcastUtil::doBroadCast("user-".$user->msisdn, "cartis-pay-account-assigned",
                ['user' => $user->toArray(), 'account_number' => $user->star_account_number,]);

            list($this->httpResponseArray, $this->statusCode) = [["code" => 200,'trace_id' => $this->requestTraceId,
                "message" => "Broad Cast Complete", "data" => []],200];
        } catch (Exception $e) {
            Log::error("[API][BroadcastController][accountAssignedBroadcast][".$this->requestTraceId."]\t 
            Error...".$e->getMessage());
            Log::error("[API][BroadcastController][accountAssignedBroadcast][".$this->requestTraceId."]\t
             Error...". $e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }

        Log::info("[API][BroadcastController][accountAssignedBroadcast][" . $this->requestTraceId . "]\t 
        final http response ...\t status: " .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }


    /**
     * Fake transaction events
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function transactionBroadcast(Transaction  $transaction, Request $request): JsonResponse
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
            BroadcastUtil::doBroadCast("user-".$transaction->user->msisdn,$request->event,['transaction' =>
                $transaction->toArray(), 'recipientName' => $beneficiary->fullName, 'accountNumber' => $accountNumber,
                "transactionType" => $transactionType, "charge" => null, "provider" => $provider]);

            list($this->httpResponseArray, $this->statusCode) = [["code" => 200,'trace_id' => $this->requestTraceId,
                "message" => "Broad Cast Complete", "data" => []],200];
        } catch (Exception $e) {
            Log::error("[API][BroadcastController][transactionBroadcast][".$this->requestTraceId."]\t 
            Error...".$e->getMessage());
            Log::error("[API][BroadcastController][transactionBroadcast][".$this->requestTraceId."]\t
             Error...". $e->getTraceAsString());

            list($this->httpResponseArray, $this->statusCode) = [["code" => 400,'trace_id' => $this->requestTraceId,
                "message" => "application error", "data" => []],400];
        }

        Log::info("[API][BroadcastController][transactionBroadcast][" . $this->requestTraceId . "]\t 
        final http response ...\t status: " .$this->statusCode."\t",$this->httpResponseArray);

        return response()->json($this->httpResponseArray,$this->statusCode);
    }

}
