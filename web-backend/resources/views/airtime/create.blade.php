@extends('layouts.app', [
    'class' => 'sidebar-mini text-center',
    'namePage' => 'Add New Airtime Transaction',
    'activePage' => 'transactions.airtime.create',
    'activeNav' => 'transactions.airtime.create',
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
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h5 class="title">{{__(" Sent Instant Airtime")}}</h5>
          </div>
          <div class="card-body">

               {!! Form::open(['action' => 'AirtimeController@getAllowedPackages',"id" => "airtimeForm"]) !!}

                  <div class="row user_mobile_wallet_row" >

                      <div class="offset-2 col-md-8 pr-1">
                          <div class="form-group">
                              {!! Form::label('msisdn',__("Beneficiary Mobile Number"), ['class'=>'text-center',]) !!}
                              <div class="input-group">
                                  {!! Form::tel('msisdn[user]',old('msisdn[user]',null),['class' => 'form-control','id' => 'msisdn', 'required']) !!}

                              </div>

                              <div class="input-group">
                                  {!! Form::text('name',old('name',null),['class' => 'form-control','id' => 'name', 'required', 'placeholder' => "Receiver Name"]) !!}
                              </div>

                              @include('alerts.feedback', ['field' => 'msisdn'])
                          </div>
                      </div>
                  </div>

                    {!! Form::hidden('iso_code',null,['id'=>'iso_code']) !!}
                  <div class="card-footer ">
                      <button type="submit" class="btn btn-info btn-round">{{__('See Airtime Packages')}}</button>
                  </div>
                  <hr class="half-rule"/>

              {!! Form::close() !!}
          </div>

      </div>
    </div>
    </div>
  </div>
@endsection

@section('js')
    <script src="{{asset('assets/plugins/intl-tel-input-master/build/js/intlTelInput.js')}}"></script>
@endsection