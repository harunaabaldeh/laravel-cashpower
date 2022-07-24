@extends('layouts.app', [
    'namePage' => 'Register page',
    'activePage' => 'register',
    'backgroundImage' => asset('assets') . "/img/bg16.jpg",
])

@section('styles')
    <link href="{{ asset('assets/plugins/intl-tel-input-master/build/css/intlTelInput.css') }}" rel="stylesheet" />
    <style>
        .iti--separate-dial-code .iti__selected-dial-code{
            color: #607d8b;
        }
    </style>
@endsection
@section('content')
  <div class="content">
    <div class="container">
      <div class="row">
        <div class="col-md-5 ml-auto">
          <div class="info-area info-horizontal mt-5">
            <div class="icon icon-primary">
              <i class="now-ui-icons media-2_sound-wave"></i>
            </div>
            <div class="description">
              <h5 class="info-title">{{ __('Instant Transfer') }}</h5>
              <p class="description">
                {{ __("We make your transfers happen at the speed of light. Join us and see how.") }}
              </p>
            </div>
          </div>
          <div class="info-area info-horizontal">
            <div class="icon icon-primary">
              <i class="now-ui-icons business_money-coins"></i>
            </div>
            <div class="description">
              <h5 class="info-title">{{ __('A Soft Bank Solution') }}</h5>
              <p class="description">
                {{ __("User cartis Pay as your electronic/soft bank - mostly an alternate banking solution") }}
              </p>
            </div>
          </div>
          <div class="info-area info-horizontal">
            <div class="icon icon-info">
              <i class="now-ui-icons transportation_air-baloon"></i>
            </div>
            <div class="description">
              <h5 class="info-title">{{ __('Utilities & More') }}</h5>
              <p class="description">
                {{ __('Let\'s help you pay for Electricity and other Utilities') }}
              </p>
            </div>
          </div>
        </div>

        <div class="col-md-4 mr-auto">
          <div class="card card-signup text-center">
            <div class="card-header ">
              <h4 class="card-title">{{ __('Register') }}</h4>
{{--              <div class="social">--}}
{{--                <button class="btn btn-icon btn-round btn-twitter">--}}
{{--                  <i class="fab fa-twitter"></i>--}}
{{--                </button>--}}
{{--                <button class="btn btn-icon btn-round btn-dribbble">--}}
{{--                  <i class="fab fa-dribbble"></i>--}}
{{--                </button>--}}
{{--                <button class="btn btn-icon btn-round btn-facebook">--}}
{{--                  <i class="fab fa-facebook-f"></i>--}}
{{--                </button>--}}
{{--                <h5 class="card-description">  {{ __('') }}</h5>--}}
{{--              </div>--}}
            </div>

              <div class="card-body ">
              <form method="POST" action="{{ route('register') }}" id="registrationForm">
                @csrf
                <!--Begin input name -->

                    <input type="hidden" name="dial_code" value="" id="dial_code">
                    <input type="hidden" name="iso_code" value="" id="iso_code">
                <!--Begin input msisdn -->

                    <div class="input-group {{ $errors->has('msisdn') ? ' has-danger' : '' }}">

                        <input class="form-control {{ $errors->has('msisdn') ? ' is-invalid' : '' }}" placeholder="{{ __('mobile number') }}" id="msisdn" type="tel" name="msisdn[user]" value="{{ old('msisdn.user') }}" required autofocus>

                        @if ($errors->has('msisdn'))
                            <span class="invalid-feedback" style="display: block;" role="alert">
                        <strong>{{ $errors->first('msisdn') }}</strong>
                    </span>
                        @endif
                    </div>


                    <div class="input-group {{ $errors->has('firstname') ? ' has-danger' : '' }}">

                    <input class="form-control {{ $errors->has('firstname') ? ' is-invalid' : '' }}" placeholder="{{ __('First Name') }}" id="firstname" type="text" name="firstname" value="{{ old('firstname') }}" required>

                    @if ($errors->has('firstname'))
                        <span class="invalid-feedback" style="display: block;" role="alert">
                        <strong>{{ $errors->first('firstname') }}</strong>
                    </span>
                    @endif
                </div>


                    <div class="input-group {{ $errors->has('lastname') ? ' has-danger' : '' }}">

                    <input class="form-control {{ $errors->has('lastname') ? ' is-invalid' : '' }}" placeholder="{{ __('Last Name') }}" id="lastname" type="text" name="lastname" value="{{ old('lastname') }}" required>

                    @if ($errors->has('lastname'))
                        <span class="invalid-feedback" style="display: block;" role="alert">
                        <strong>{{ $errors->first('lastname') }}</strong>
                    </span>
                    @endif
                </div>




                <!--Begin input user type-->

                <!--Begin input password -->
                <div class="input-group {{ $errors->has('password') ? ' has-danger' : '' }}">
                  <input class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('Password') }}" type="password" name="password" required>
                  @if ($errors->has('password'))
                    <span class="invalid-feedback" style="display: block;" role="alert">
                      <strong>{{ $errors->first('password') }}</strong>
                    </span>
                  @endif
                </div>
                <!--Begin input confirm password -->
                <div class="input-group">
                  <input class="form-control" placeholder="{{ __('Confirm Password') }}" type="password" name="password_confirmation" required>
                </div>
                <div class="form-check text-left">
                  <label class="form-check-label">
                    <input class="form-check-input" type="checkbox">
                    <span class="form-check-sign"></span>
                    {{ __('I agree to the') }}
                    <a href="#something">{{ __('terms and conditions') }}</a>.
                  </label>
                </div>
                <div class="card-footer ">
                  <button type="submit" class="btn btn-primary btn-round btn-lg">{{__('Get Started')}}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js')
    <script src="{{asset('assets/plugins/intl-tel-input-master/build/js/intlTelInput.js')}}"></script>
  <script>
    $(document).ready(function() {
      demo.checkFullPageBackgroundImage();
    });

  </script>
@endsection
