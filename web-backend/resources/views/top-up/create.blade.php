@extends('layouts.app', [
    'class' => 'sidebar-mini text-center',
    'namePage' => 'Account Top Up',
    'activePage' => 'accounts.top-up',
    'activeNav' => 'accounts.top-up',
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
            <h5 class="title">{{__(" Account Top Up")}}</h5>
          </div>
          <div class="card-body">

               {!! Form::open(['route' => ['accounts.top-up.post',\Auth::user()->uuid],"id" => "account top-up"]) !!}

                  <div class="row user_mobile_wallet_row" >

                      <div class="offset-2 col-md-8 pr-1">

                          <div class="input-group mb-3">
                              <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1">{{$destination_currency}}</span>
                              </div>
                              {!! Form::number('amount',old('amount',null),['class' => 'form-control','id' => 'amount', 'required', 'step' => .01]) !!}
                          </div>
                      </div>
                  </div>

                  <div class="card-footer ">
                      <button type="submit" class="btn btn-info btn-round">{{__('TopUp Account')}}</button>
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