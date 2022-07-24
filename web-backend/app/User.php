<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['firstname', 'lastname', 'othernames', 'msisdn', 'star_account_number','properties', 'email'
        , 'password','country_id', 'address','city','state','idType','idNumber','dateOfBirth','uuid','balance',
        'api_token'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','properties','api_token','deleted_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function authenticationTokens(){
        return $this->hasMany(AuthenticationToken::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function country(){
        return $this->belongsTo('\App\Country');
    }

    public function getBalanceAttribute($balance){
        return floatval($balance);
    }


    public function getFullNameAttribute(){
        return "{$this->firstname} {$this->lastname} {$this->othernames}";
    }

    public function setIsAdminUserAttribute($isAdminUser){
        $properties = json_decode($this->properties,true);

        if (empty($properties) || !is_array($properties))
            $properties = [];

        $properties['is-admin-user'] = $isAdminUser;

        $this->attributes['properties'] = json_encode($properties);
    }

    public function getIsAdminUserAttribute(){
        $properties =$this->properties;

        if (!empty($properties)){
            $properties = json_decode($properties,true);
            if (is_array($properties) && array_key_exists('is-admin-user',$properties)){
                return $properties['is-admin-user'];
            }
        }
        return false;
    }


    public function setIsAgentUserAttribute($isAgentUser){
        $properties = json_decode($this->properties,true);

        if (empty($properties) || !is_array($properties))
            $properties = [];

        $properties['is-agent-user'] = $isAgentUser;

        $this->attributes['properties'] = json_encode($properties);
    }

    public function getIsAgentUserAttribute(){
        $properties =$this->properties;

        if (!empty($properties)){
            $properties = json_decode($properties,true);
            if (is_array($properties) && array_key_exists('is-agent-user',$properties)){
                return $properties['is-agent-user'];
            }
        }
        return null;
    }


    public function setAccountStatusAttribute($accountStatus){
        $properties = json_decode($this->properties,true);

        if (empty($properties) || !is_array($properties))
            $properties = [];

        $properties['account-status'] = $accountStatus;

        $this->attributes['properties'] = json_encode($properties);
    }

    public function getAccountStatusAttribute(){
        $properties =$this->properties;

        if (!empty($properties)){
            $properties = json_decode($properties,true);
            if (is_array($properties) && array_key_exists('account-status',$properties)){
                return $properties['account-status'];
            }
        }
        return "None";
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
