<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Biller extends Model
{
    protected $fillable = ['name','uuid','country_id','category','properties'];

    public function country(){
        return $this->belongsTo('\App\Country');
    }


    public function setZeepayIdAttribute($transactionReference){
        $properties = json_decode($this->properties,true);

        if (empty($properties) || !is_array($properties))
            $properties = [];
        $properties['zeepayId'] = $transactionReference;

        $this->attributes['properties'] = json_encode($properties);
    }

    public function getZeepayIdAttribute(){
        $properties =$this->properties;

        if (!empty($properties)){
            $properties = json_decode($properties,true);
            if (is_array($properties) && array_key_exists('zeepayId',$properties)){
                return $properties['zeepayId'];
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
