<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;
    protected $fillable = ['gateway_id','reference','status','status_message','source_currency','destination_currency','source_amount','destination_amount','user_id','rate_id','processor','properties'];

    public function rate(){
        return $this->belongsTo('\App\Rate');
    }

    public function user(){
        return $this->belongsTo('\App\User');
    }

}
