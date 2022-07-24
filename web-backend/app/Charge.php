<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Charge extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_type',
        'service_name',
        'fixed_charge',
        'percentage_charge',
        'source_country',
        'destination_country',
        'properties',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['properties'];

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format("Y-m-d H:i:s");
    }

    public function getUpdatedAtAttribute($date)
    {
        return  Carbon::parse($date)->format("Y-m-d H:i:s");
    }
}
