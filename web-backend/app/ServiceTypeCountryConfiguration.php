<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceTypeCountryConfiguration extends Model
{
    protected $fillable = ['service_name','countries','properties'];

    public function getCountriesAttribute($countries){
        return json_decode($countries,true);
    }

    public function getCreatedAtAttribute($date)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
    }

    public function getUpdatedAtAttribute($date)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
    }
}
