<?php

namespace App\Http\Controllers\Auth;

use App\AuthenticationToken;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilitiesController;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/verify-authentication-code';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        Log::info("[Auth][RegisterController][validator]\tcalled... ",$data);
        $validator = Validator::make($data, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'dial_code' => ['required', 'string', 'max:255'],
            'iso_code' => ['required', 'string', 'max:255'],
            'msisdn.user' => ['required', 'string', 'max:255'],
            'msisdn.full' => ['required', 'string', 'max:255', 'unique:users,msisdn'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Log::info("[Auth][RegisterController][validator]\tfirst stage done");

        Log::info("[Auth][RegisterController][validator]\tvalidation called");

        $full_msisdn = $data['msisdn']['full'];

        if (!empty(\App\User::where('msisdn',substr($full_msisdn,1))->withTrashed()->first())){
            Log::info("user exists");
            $validator->errors()->add('msisdn', 'Account Already Exists');
        }


        Log::info("[Auth][RegisterController][validator]\tfull msisdn: \t".$full_msisdn);



        $country = \Countries::where('calling_code',$data['dial_code'])->first();

        if (empty($country)){
            $country = \Countries::where('iso_3166_2',strtoupper(trim($data['iso_code'])))->first();
            if (empty($country)){
                $validator->errors()->add('msisdn', 'Country Resolution Failed');
            }
        }

        Log::info("[Auth][RegisterController][validator]\tcountry: \t",$country->toArray());

        if ($validator->fails())
        {

            Log::error("[Auth][RegisterController][validator]\tvalidation failed: \t",$country->toArray());
//            return redirect()->back()->withErrors($validator)->withInput();
            return $validator;
        }

        return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @param Request $request
     * @return \App\User
     */
    protected function create(array $data)
    {
        $full_msisdn = $data['msisdn']['full'];

        $country = \Countries::where('calling_code',$data['dial_code'])->first();

        if (empty($country)){
            $country = \Countries::where('iso_3166_2',strtoupper(trim($data['iso_code'])))->first();
        }

        try{

            $user = User::where('msisdn',substr($full_msisdn, 1))->first();

            if (!empty($user)){
                return  $user;
            }


            $user = User::create(['uuid' => Uuid::uuid4()->toString(),'password' => Hash::make($data['password']),
                'firstname' => $data['firstname'], 'lastname' => $data['lastname'],
                'msisdn' => substr($full_msisdn, 1), 'country_id' => $country->id]);
            $user->uuid = Uuid::uuid4()->toString();
            $user->api_token = hash('sha256', Uuid::uuid4()->toString());
            $user->save();
            UtilitiesController::generateAndSendOTP($user,$data['password']);
            dispatch(new \App\Jobs\AssignStarAccount($user));
            return $user;
        }catch (\Exception $exception){
            $errorString = $exception->getMessage();
            Log::error("[RegistersUsers][create]\t Error\t".$errorString);
            Log::error("[RegistersUsers][create]\t Error\t".$exception->getTraceAsString());

            if (Str::contains($errorString,"Integrity constraint violation: 1062 Duplicate entry")){
                if (Str::contains($errorString,"users.users_msisdn_unique")){
                    request()->session()->flash("error","Mobile Number Already Taken");
                    return redirect()->back();
                }

            }
        }
    }

}
