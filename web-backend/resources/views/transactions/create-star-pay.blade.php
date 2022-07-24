@extends('layouts.app', [
    'class' => 'sidebar-mini text-center',
    'namePage' => 'Add New Transaction',
    'activePage' => 'transactions',
    'activeNav' => 'transactions',
])
@section('styles')
{{--    <link href="{{ asset('assets/plugins/intl-tel-input-master/build/css/intlTelInput.css') }}" rel="stylesheet" />--}}
@endsection
@section('content')
  <div class="panel-header panel-header-sm">
  </div>
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h5 class="title">{{__(" Cartis Pay To Cartis Pay Accounts")}}</h5>
          </div>
          <div class="card-body">

               {!! Form::open(['action' => 'TransactionsController@intraStarPayAccountTransfers']) !!}

            @include('alerts.success')
            <div class="row">
            </div>


            <div class="row">
              <div class="offset-2 col-md-8 pr-1">
                <div class="form-group">
                  {!! Form::label('beneficiary_id',__(" Beneficiary"), ['class'=>'text-center']) !!}
                  {{Form::number('star_account_number',old('star_account_number',null),['min' => 1000000000,'id' => 'star_account_number','class' => 'form-control star_account_number','required','placeholder' => "Provide Recipient Cartis Pay Account Number"])}}
                  @include('alerts.feedback', ['field' => 'star_account_number'])
                </div>
              </div>
            </div>

            <div class="showOnAccountResolve">



            <div class="row">
              <div class="offset-2 col-md-8 pr-1">
                <div class="form-group">
                  {!! Form::label('beneficiary_name',__(" Beneficiary Name "), ['class'=>'text-center']) !!}
                  {{Form::text('beneficiary_name',old('beneficiary_name',null),['id' => 'beneficiary_name','class' => 'form-control ','required','disabled'])}}
                  @include('alerts.feedback', ['field' => 'beneficiary_name'])
                </div>
              </div>
            </div>


            <div class="row">
              <div class="offset-2 col-md-8 pr-1">
                <div class="form-group">
                  {!! Form::label('beneficiary_msisdn',__(" Beneficiary Phone "), ['class'=>'text-center']) !!}
                  {{Form::text('beneficiary_msisdn',old('beneficiary_msisdn',null),['id' => 'beneficiary_msisdn','class' => 'form-control','required','disabled'])}}
                  @include('alerts.feedback', ['field' => 'beneficiary_msisdn'])
                </div>
              </div>
            </div>


            <div class="row">
              <div class="offset-2 col-md-8 pr-1">
                <div class="form-group">
                  {!! Form::label('beneficiary_country',__(" Beneficiary Country "), ['class'=>'text-center']) !!}
                  {{Form::text('beneficiary_country',old('beneficiary_country',null),['id' => 'beneficiary_country','class' => 'form-control','required','disabled'])}}
                  @include('alerts.feedback', ['field' => 'beneficiary_country'])
                </div>
              </div>
            </div>


            <div class="row pt-3">
              <div class="offset-2 col-md-8 pr-1">
                <div class="form-group">
                  <div class="input-group ">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="send_currency_symbol">{{$user_country->currency_code}}</span>
                    </div>
                    {{Form::number('source_amount',old('source_amount',null),['step' => 0.01,'min' => 1,'id' => 'source_amount','class' => 'form-control ','required','placeholder' => "How much you send", 'disabled','required'])}}
                  </div>
                </div>
              </div>
            </div>

            <div class="row pt-3">
              <div class="offset-2 col-md-8 pr-1">
                <div class="form-group">
                  <div class="input-group ">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="destination_currency_symbol"> ____ </span>
                    </div>
                    {{Form::number('destination_amount',old('destination_amount',null),['step' => 0.01,'min' => 1,'id' => 'destination_amount','class' => 'form-control','required','placeholder' => "How much they receive", 'disabled','required'])}}
                  </div>
                </div>
              </div>
            </div>

              {{Form::hidden("rate_id",0,['id' => 'rate_id'])}}
              {{Form::hidden("type",$service_type,['id' => 'service_type'])}}

              <script>
                var authUser = JSON.parse('{!! $user !!}');
                window.localStorage.setItem("x-auth-user",JSON.stringify(authUser));
              </script>

              <div class="card-footer ">
                <button type="submit" class="btn btn-info btn-round">{{__('Confirm & Create Transaction')}}</button>
              </div>
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
{{--    <script src="{{asset('assets/plugins/intl-tel-input-master/build/js/intlTelInput.js')}}"></script>--}}
@endsection