<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = ['reference','gateway_id','type','status','status_message','source_currency','purpose',
        'api_reference', 'destination_currency','source_amount','destination_amount','user_id','rate_id',
        'beneficiary_id','properties','partner_receipt_reference'];

    public function beneficiary(){
        return $this->belongsTo('\App\Beneficiary');
    }

    public function rate(){
       return $this->belongsTo('\App\Rate');
    }

    public function user(){
       return $this->belongsTo('\App\User');
    }


    public function setPaymentIntentAttribute($transactionReference){
        $properties = json_decode($this->properties,true);

        if (empty($properties) || !is_array($properties))
            $properties = [];
        $properties['paymentIntent'] = $transactionReference;

        $this->attributes['properties'] = json_encode($properties);
    }

    public function getPaymentIntentAttribute(){
        $properties =$this->properties;

        if (!empty($properties)){
            $properties = json_decode($properties,true);
            if (is_array($properties) && array_key_exists('paymentIntent',$properties)){
                return $properties['paymentIntent'];
            }

        }

        return null;
    }


    public function setAirtimeMsisdnAttribute($transactionReference){
        $properties = json_decode($this->properties,true);

        if (empty($properties) || !is_array($properties))
            $properties = [];

        $properties['airtime-msisdn'] = $transactionReference;

        $this->attributes['properties'] = json_encode($properties);
    }

    public function getAirtimeMsisdnAttribute(){
        $properties =$this->properties;

        if (!empty($properties)){
            $properties = json_decode($properties,true);
            if (is_array($properties) && array_key_exists('airtime-msisdn',$properties)){
                return $properties['airtime-msisdn'];
            }

        }

        return null;
    }

    public function setAirtimeReceiverCountryAttribute($transactionReference){
        $properties = json_decode($this->properties,true);

        if (empty($properties) || !is_array($properties))
            $properties = [];

        $properties['airtime-receiver-country'] = $transactionReference;

        $this->attributes['properties'] = json_encode($properties);
    }

    public function getAirtimeReceiverCountryAttribute(){
        $properties =$this->properties;

        if (!empty($properties)){
            $properties = json_decode($properties,true);
            if (is_array($properties) && array_key_exists('airtime-receiver-country',$properties)){
                return $properties['airtime-receiver-country'];
            }

        }

        return null;
    }

    public function setAirtimeReceiverNameAttribute($transactionReference){
        $properties = json_decode($this->properties,true);

        if (empty($properties) || !is_array($properties))
            $properties = [];

        $properties['airtime-receiver-name'] = $transactionReference;

        $this->attributes['properties'] = json_encode($properties);
    }

    public function getAirtimeReceiverNameAttribute(){
        $properties =$this->properties;

        if (!empty($properties)){
            $properties = json_decode($properties,true);
            if (is_array($properties) && array_key_exists('airtime-receiver-name',$properties)){
                return $properties['airtime-receiver-name'];
            }

        }

        return null;
    }

    public function setMeterNumberAttribute($meterNumber){
        $properties = json_decode($this->properties,true);

        if (empty($properties) || !is_array($properties))
            $properties = [];

        $properties['pre-paid-meter-number'] = $meterNumber;

        $this->attributes['properties'] = json_encode($properties);
    }

    public function GetMeterNumberAttribute(){
        $properties =$this->properties;

        if (!empty($properties)){
            $properties = json_decode($properties,true);
            if (is_array($properties) && array_key_exists('pre-paid-meter-number',$properties)){
                return $properties['pre-paid-meter-number'];
            }

        }

        return null;
    }

    public function setPrePaidPhoneNumberAttribute($meterNumber){
        $properties = json_decode($this->properties,true);

        if (empty($properties) || !is_array($properties))
            $properties = [];

        $properties['pre-paid-phone-number'] = $meterNumber;

        $this->attributes['properties'] = json_encode($properties);
    }

    public function getPrePaidPhoneNumberAttribute(){
        $properties =$this->properties;

        if (!empty($properties)){
            $properties = json_decode($properties,true);
            if (is_array($properties) && array_key_exists('pre-paid-phone-number',$properties)){
                return $properties['pre-paid-phone-number'];
            }

        }

        return null;
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format("Y-m-d H:i:s");
    }

    public function getUpdatedAtAttribute($date)
    {
        return  Carbon::parse($date)->format("Y-m-d H:i:s");
    }
}
