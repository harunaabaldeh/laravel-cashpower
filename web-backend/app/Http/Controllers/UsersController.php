<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    public function resolveUserByStarPayAccount($accountNumber, Request $request){

        Log::alert("",$request->toArray());

        if ($request->has('api_token')){
            $authUser = \App\User::where('api_token',$request->input('api_token'))->first();

            if (!empty($authUser)){
                $user = \App\User::where('star_account_number',$accountNumber)->first();
                if (!empty($user)){
                    $rate = \App\Rate::where(['source_currency' => $authUser->country->currency_code,
                        'destination_currency' => $user->country->currency_code])->latest()->first();

                    return  array("user" => $user, "country" => $user->country, "rate" => $rate);
                }
            }
        }

        return [];
    }
}
