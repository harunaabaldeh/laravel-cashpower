@extends('layouts.app', [
    'class' => 'sidebar-mini text-center',
    'namePage' => 'Airtime Package Selection',
    'activePage' => 'transactions.airtime.create',
    'activeNav' => 'transactions.airtime.create',
])
@section('styles')
    <link href="{{ asset('assets/plugins/intl-tel-input-master/build/css/intlTelInput.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div class="panel-header panel-header-sm">
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="title">Select Airtime Package For <span class="text-danger">{{$msisdn}}</span></h5>
                        <p class="title">Mobile Network Name: <span class="text-danger">{{$mno}}</span></p>
                    </div>
                    <div class="card-body all-icons">
                        <div class="row">
                            @foreach($packages as $package)
                            <div class="font-icon-list col-lg-2 col-md-3 col-sm-4 col-xs-6 col-xs-6">
                               <a form-id="airtime-package-select-{{trim($package)}}" href="#" class="card-link airtimeConfirm" form-alert-message="Please Confirm. Receive Airtime Amount {{$currency}} {{$package}}. You Pay {{$source_currency}} {{round(($package/$rate), 2, PHP_ROUND_HALF_UP)}}" >
                                   <div class="font-icon-detail bg-light">
                                       {{$currency}} <strong>{{$package}}</strong>
                                       <p class="text-info">You Pay: {{$source_currency}} <strong>{{round(($package/$rate), 2, PHP_ROUND_HALF_UP)}}</strong></p>
                                   </div>
                               </a>
                                {!! Form::open(['action' => 'AirtimeController@createAirtimeTransaction',"id" => "airtime-package-select-".trim($package)]) !!}
                                    {!! Form::hidden('source_currency',$source_currency) !!}
                                    {!! Form::hidden('destination_currency',$currency) !!}
                                    {!! Form::hidden('source_amount',round(($package/$rate), 2, PHP_ROUND_HALF_UP)) !!}
                                    {!! Form::hidden('destination_amount',$package) !!}
                                    {!! Form::hidden('msisdn',$msisdn) !!}
                                    {!! Form::hidden('type',"Airtime") !!}
                                    {!! Form::hidden('rate_id',$rate_id) !!}
                                    {!! Form::hidden('name',$name) !!}
                                    {!! Form::hidden('destination_country_id',$destination_country_id) !!}
                                {!! Form::close() !!}
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{asset('assets/plugins/intl-tel-input-master/build/js/intlTelInput.js')}}"></script>
@endsection