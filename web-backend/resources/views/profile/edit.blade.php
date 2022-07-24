@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'User Profile',
    'activePage' => 'profile',
    'activeNav' => '',
])

@section('styles')
  <link href="{{ asset('assets/plugins/date-picker/dist/datepicker.css') }}" rel="stylesheet" />
@endsection

@section('content')
  <div class="panel-header panel-header-sm">
  </div>
  <div class="content">
    <div class="row">
      <div class="col-md-8">
        <div class="card ">
          <div class="card-header text-center">
            <h5 class="title">{{__(" Edit Profile")}}</h5>
            <h6>{{__(" Star Pay Account Number: ")}} <span class="text-info">{{auth()->user()->star_account_number}}</span></h6>
            <h6>{{__("Mobile Number: ")}} <span class="text-info">{{auth()->user()->msisdn}}</span></h6>
          </div>
          <div class="card-body text-center">
            <form method="post" action="{{ route('profile.update') }}" autocomplete="off" enctype="multipart/form-data">
              @csrf
              @method('put')
{{--              @include('alerts.success')--}}
              <div class="row">
              </div>
                <div class="row text-center">
                    <div class="col-md-5 pr-1">
                        <div class="form-group">
                            <label>{{__(" First Name")}}</label>
                                <input type="text" name="firstname" class="form-control" value="{{ old('firstname', auth()->user()->firstname) }}">
                                @include('alerts.feedback', ['field' => 'firstname'])
                        </div>
                    </div>
                  <div class="col-md-5 pr-1">
                    <div class="form-group">
                      <label>{{__(" Last Name")}}</label>
                      <input type="text" name="lastname" class="form-control" value="{{ old('lastname', auth()->user()->lastname) }}">
                      @include('alerts.feedback', ['field' => 'lastname'])
                    </div>
                  </div>
                </div>

                <div class="row">
                    <div class="col-md-5 pr-1">
                        <div class="form-group">
                            <label>{{__(" Other Name(s) - Optional")}}</label>
                                <input type="text" name="othernames" class="form-control" value="{{ old('othernames', auth()->user()->othernames) }}">
                                @include('alerts.feedback', ['field' => 'othernames'])
                        </div>
                    </div>

                  <div class="col-md-5 pr-1">
                    <div class="form-group">
                      <label>{{__(" Date Of Birth")}}</label>
                      <input type="text" name="dateOfBirth" class="form-control datePicker" value="{{ old('dateOfBirth', auth()->user()->dateOfBirth) }}">
                      @include('alerts.feedback', ['field' => 'dateOfBirth'])
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-5 pr-1">
                    <div class="form-group">
                      <label for="exampleInputEmail1">{{__(" Email address")}}</label>
                      <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email', auth()->user()->email) }}">
                      @include('alerts.feedback', ['field' => 'email'])
                    </div>
                  </div>
                  <div class="col-md-5 pr-1">
                    <div class="form-group">
                      <label for="exampleInputEmail1">{{__("Address")}}</label>
                      <input type="text" name="address" class="form-control" placeholder="Address" value="{{ old('address', auth()->user()->address) }}">
                      @include('alerts.feedback', ['field' => 'address'])
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-5 pr-1">
                    <div class="form-group">
                      <label for="exampleInputEmail1">{{__(" City")}}</label>
                      <input type="text" name="city" class="form-control" placeholder="city" value="{{ old('city', auth()->user()->city) }}">
                      @include('alerts.feedback', ['field' => 'city'])
                    </div>
                  </div>
                  <div class="col-md-5 pr-1">
                    <div class="form-group">
                      <label for="exampleInputEmail1">{{__("State")}}</label>
                      <input type="text" name="state" class="form-control" placeholder="state" value="{{ old('state', auth()->user()->state) }}">
                      @include('alerts.feedback', ['field' => 'state'])
                    </div>
                  </div>
                </div>


                <div class="row">
                  <div class="col-md-5 pr-1">
                    <div class="form-group">
                      <label for="exampleInputEmail1">{{__("ID Type")}}</label>
{{--                      <input type="text" name="idType" class="form-control" placeholder="Id Type" value="{{ old('idType', auth()->user()->idType) }}">--}}
                      {!! Form::select('idType',$idTypes,old('idType', auth()->user()->idType),['class'=>'form-control text-center','id' =>'idType', 'placeholder' => 'Select Your Type Of ID']) !!}

                      @include('alerts.feedback', ['field' => 'idType'])
                    </div>
                  </div>
                  <div class="col-md-5 pr-1">
                    <div class="form-group">
                      <label for="exampleInputEmail1">{{__("Id Number")}}</label>
                      <input type="text" name="idNumber" class="form-control" placeholder="Id Number" value="{{ old('idNumber', auth()->user()->idNumber) }}">
                      @include('alerts.feedback', ['field' => 'idNumber'])
                    </div>
                  </div>
                </div>


              <div class="card-footer ">
                <button type="submit" class="btn  btn-round btn-info">{{__('Update Profile')}}</button>
              </div>
              <hr class="half-rule"/>
            </form>
          </div>

      </div>
    </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-header text-center">
            <h5 class="title">{{__("Change Password")}}</h5>
          </div>
          <div class="card-body">
            <form method="post" action="{{ route('profile.password') }}" autocomplete="off">
              @csrf
              @method('put')
              @include('alerts.success', ['key' => 'password_status'])
              <div class="row text-center">
                <div class="col-md-12">
                  <div class="form-group {{ $errors->has('password') ? ' has-danger' : '' }}">
                    <label>{{__(" Current Password")}}</label>
                    <input class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" name="old_password" placeholder="{{ __('Current Password') }}" type="password"  required>
                    @include('alerts.feedback', ['field' => 'old_password'])
                  </div>
                </div>
              </div>
              <div class="row text-center">
                <div class="col-md-12 ">
                  <div class="form-group {{ $errors->has('password') ? ' has-danger' : '' }}">
                    <label>{{__(" New password")}}</label>
                    <input class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('New Password') }}" type="password" name="password" required>
                    @include('alerts.feedback', ['field' => 'password'])
                  </div>
                </div>
              </div>
              <div class="row text-center">
                <div class="col-md-12 ">
                  <div class="form-group {{ $errors->has('password') ? ' has-danger' : '' }}">
                    <label>{{__(" Confirm New Password")}}</label>
                    <input class="form-control" placeholder="{{ __('Confirm New Password') }}" type="password" name="password_confirmation" required>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-info btn-round btn-block">{{__('Change Password')}}</button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
@endsection

@section('js')
  <script src="{{asset('assets/plugins/date-picker/dist/datepicker.js')}}"></script>
@endsection