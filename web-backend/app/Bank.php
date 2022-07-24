<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use SoftDeletes;

    protected $fillable = ['name','routing_code','country_code','properties'];

    protected $hidden = ['properties','deleted_at'];
}
