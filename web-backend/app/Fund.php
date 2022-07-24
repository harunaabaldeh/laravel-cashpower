<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fund extends Model
{
    use SoftDeletes;

    protected $fillable = ['amount','balance_before','balance_after','description','properties','user_id','payment_id',
        'transaction_id','type'];


    public function user(){
        return $this->belongsTo('\App\User');
    }

    public function payment(){
        return $this->belongsTo('\App\Payment');
    }

    public function transaction(){
        return $this->belongsTo('\App\Transaction');
    }

}
