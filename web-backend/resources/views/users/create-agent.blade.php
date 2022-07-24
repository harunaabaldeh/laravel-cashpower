@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Add New Agent User',
    'activePage' => 'user',
    'activeNav' => '',
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
    <div class="panel-header panel-header-sm">
    </div>
    <div class="content">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Agent User Management') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('user.index') }}" class="btn btn-primary btn-round">{{ __('Back to list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('users.agent-user.store') }}" autocomplete="off">
                            @csrf

                            <h6 class="heading-small text-muted mb-4">{{ __('Agent information') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('firstname') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-firstname">{{ __('Firstname') }}</label>
                                    <input type="text" name="firstname" id="input-firstname" class="form-control{{ $errors->has('firstname') ? ' is-invalid' : '' }}" placeholder="{{ __('Firstname') }}" value="{{ old('firstname') }}" required autofocus>

                                    @include('alerts.feedback', ['field' => 'firstname'])
                                </div>
                                <div class="form-group{{ $errors->has('lastname') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-lastname">{{ __('Lastname') }}</label>
                                    <input type="text" name="lastname" id="input-lastname" class="form-control{{ $errors->has('lastname') ? ' is-invalid' : '' }}" placeholder="{{ __('Lastname') }}" value="{{ old('lastname') }}" required>

                                    @include('alerts.feedback', ['field' => 'lastname'])
                                </div>
                                <div class="form-group{{ $errors->has('othernames') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-othernames">{{ __('Other name(s)') }}</label>
                                    <input type="text" name="othernames" id="input-othernames" class="form-control{{ $errors->has('othernames') ? ' is-invalid' : '' }}" placeholder="{{ __('Other name(s)') }}" value="{{ old('othernames') }}">

                                    @include('alerts.feedback', ['field' => 'othernames'])
                                </div>

                                <div class="form-group{{ $errors->has('msisdn') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-msisdn">{{ __('Mobile Number') }}</label>
                                    <input type="hidden" name="dial_code" value="" id="dial_code">
                                    <input type="hidden" name="iso_code" value="" id="iso_code">
                                    <!--Begin input msisdn -->

                                    <div class="input-group {{ $errors->has('msisdn') ? ' has-danger' : '' }}">

                                        <input class="form-control {{ $errors->has('msisdn') ? ' is-invalid' : '' }}" placeholder="{{ __('mobile number') }}" id="msisdn" type="tel" name="msisdn[user]" required>

                                        @include('alerts.feedback', ['field' => 'msisdn'])
                                    </div>

                                </div>


                                <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-password">{{ __('Password') }}</label>
                                    <input type="password" name="password" id="input-password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('Password') }}" value="" required>

                                    @include('alerts.feedback', ['field' => 'password'])
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label" for="input-password-confirmation">{{ __('Confirm Password') }}</label>
                                    <input type="password" name="password_confirmation" id="input-password-confirmation" class="form-control" placeholder="{{ __('Confirm Password') }}" value="" required>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Add Agent') }}</button>
                                </div>
                            </div>
                        </form>
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
