@extends('layouts.app', [
    'class' => 'sidebar-mini text-center',
    'namePage' => 'Initiate New Payment',
    'activePage' => 'payments.initiate',
    'activeNav' => 'payments.initiate',
])
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
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        var stripe = Stripe("{{env('stripe.pk')}}");

        stripe.redirectToCheckout({
            sessionId: '{{$checkOutSessionId}}'
        }).then(function (result) {
            if(result.hasOwnProperty('error') && result.error.hasOwnProperty('message')){
                sweetAlertDanger(result.error.message);
            }
        });
    </script>
@endsection