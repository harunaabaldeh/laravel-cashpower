<?php

namespace App\Http\Controllers\Auth;

use App\AuthenticationToken;
use App\Country;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilitiesController;
use App\Http\Requests\TwoFAVerificationRequest;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['logout',]);
    }

    public function login(Request $request)
    {

        Log::info("",$request->input());
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $country = $msisdn = null;
        if ($request->has('iso_code')){
            $country = Country::where('iso_3166_2',$request->input('iso_code'))->first();
        }

        if (empty($country)){
            $request->session()->flash("error","Invalid Country/Number Details");
            return redirect()->back()->withInput();
        }

        if ($request->has('msisdn'))
        {
            $msisdn = $request->input('msisdn');
            if (!empty($msisdn) && is_array($msisdn)){
                if (array_key_exists('full',$msisdn)){
                    $msisdn = $msisdn['full'];
                }
            }
        }

        if (!UtilitiesController::isValidMSISDN($msisdn,$country->iso_3166_2)){
            $request->session()->flash("error","Invalid Mobile Number Provided");
            return redirect()->back()->withInput();
        }


        $msisdn = UtilitiesController::formatMSISDN($msisdn,$country->iso_3166_2);
        $msisdn = str_replace(" ","",str_replace("+","",$msisdn));


        $user = \App\User::where('msisdn',$msisdn)->first();

        if (!empty($user) && Hash::check($request->input('password'), $user->password)) {

            UtilitiesController::generateAndSendOTP($user,$request->input('password'));
            return redirect()->route('verify-2fa-view');
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function username()
    {
        return 'msisdn';
    }

    public function getVerify2FAVerificationPage(Request $request){
        if (!empty(session('auth_stage')) && session('auth_stage') == "verify_otp"){
            $request->session()->flash("info","Please Provide OTP Code Sent to your Phone Number");
            return view('auth.2fa-verification');
        }else{
            $request->session()->flash("error","No OTP Assigned");
            return redirect()->route('login');
        }

    }

    public function verify2FAVerification(TwoFAVerificationRequest $request){

        if (Auth::check())
            return redirect('home');

        if (!empty(session('auth_stage')) && session('auth_stage') == "verify_otp"){

            if (!empty(session('token_id'))){
                $token = AuthenticationToken::find(session('token_id'));
                if (!empty($token) && $token->isValid()){
                    $user = $token->user;
                    $request->request->add(['msisdn' => $user->msisdn,'password' => decrypt(\Redis::get("_token_id_credentials_".$token->id))]);

                    if ($this->attemptLogin($request)){
                        \Redis::del("_token_id_credentials_".$token->id);
                        $token->used = true;
                        $token->save();

                        session()->remove('auth_stage');
                        session()->remove('token_id');

                        return $this->sendLoginResponse($request);
                    }
                }else{
                    $request->session()->flash("error","Invalid/Expired OTP");
                    return redirect()->route('login');
                }
            }

            $request->session()->flash("error","Invalid/Expired OTP");
            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);

        }else{
            $request->session()->flash("error","Authentication Error");
            return redirect()->route('login');
        }

    }
}
