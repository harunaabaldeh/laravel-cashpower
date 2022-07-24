<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $fillable = ['msisdn','account_type','firstname','lastname','nickname','othernames','account_number',
        'account_routing_number','user_id','country_id','bank_name','gender','relationship'];

    protected $hidden = ['user_id','properties','deleted_at'];

    public function getFullNameAttribute(){
        return "{$this->firstname} {$this->lastname} {$this->othernames}";
    }

    public function country(){
        return $this->belongsTo('\App\Country');
    }

    public function user(){
        return $this->belongsTo('\App\User');
    }

    public function transactions(){
        return $this->hasMany('\App\Transaction');
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
