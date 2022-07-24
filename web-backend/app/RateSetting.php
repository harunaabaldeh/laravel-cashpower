<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RateSetting extends Model
{
    use SoftDeletes;

    protected $fillable = ['source_currency','destination_currency','markup_fixed','markup_percentage','properties'];
}
