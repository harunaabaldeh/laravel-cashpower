<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class CheckKYCRequirements
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $user = \Auth::user();

        if (!$request->route()->named('profile')){


            if (empty($user->email)){
                $request->session()->flash("error","Kindly Complete Your Profile Details To Proceed");
                return Redirect::route('profile.edit');
            }


            if (empty($user->address)){
                $request->session()->flash("error","Kindly Complete Your Profile Details To Proceed");
                return Redirect::route('profile.edit');
            }

            if (empty($user->city)){
                $request->session()->flash("error","Kindly Complete Your Profile Details To Proceed");
                return Redirect::route('profile.edit');
            }


            if (empty($user->state)){
                $request->session()->flash("error","Kindly Complete Your Profile Details To Proceed");
                return Redirect::route('profile.edit');
            }

            if (empty($user->idType)){
                $request->session()->flash("error","Kindly Complete Your Profile Details To Proceed");
                return Redirect::route('profile.edit');
            }

            if (empty($user->dateOfBirth)){
                $request->session()->flash("error","Kindly Complete Your Profile Details To Proceed");
                return Redirect::route('profile.edit');
            }


        }

        return $next($request);
    }

}
