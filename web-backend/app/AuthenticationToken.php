<?php

namespace App;

use App\Http\Controllers\UtilitiesController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AuthenticationToken extends Model
{

    const  EXPIRATION_TIME = 5;
    protected $fillable = ['code','used','user_id','properties'];

    public function __construct(array $attributes = [])
    {
        if (! isset($attributes['code'])) {
            $attributes['code'] = $this->generateCode();
        }

        parent::__construct($attributes);
    }

    public function user(){
        return $this->belongsTo(\App\User::class);
    }

    public function generateCode($codeLength =4){
        $min = pow(10, $codeLength);
        $max = $min * 10 - 1;
        return mt_rand($min, $max);
    }

    public function isValid()
    {
        return ! $this->isUsed() && ! $this->isExpired();
    }

    public function isUsed()
    {
        return $this->used;
    }

    public function isExpired()
    {
        return $this->created_at->diffInMinutes(Carbon::now()) > static::EXPIRATION_TIME;
    }

    public function sendCode()
    {
        Log::info("[AuthenticationToken][sendCode]\t.. Called");
        if (! $this->user) {
            throw new \Exception("No user attached to this token.");
        }

        if (! $this->code) {
            $this->code = $this->generateCode();
        }

        $message = str_replace("@@code@@",$this->code,env("OTP_MESSAGE_TEMPLATE"));
        Log::info("[AuthenticationToken][sendCode]\t.. message after replacement\t".$message);
        UtilitiesController::sendSMS($message , $this->user->msisdn);
        return true;
    }


}
