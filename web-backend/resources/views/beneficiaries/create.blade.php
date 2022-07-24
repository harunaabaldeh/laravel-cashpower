@extends('layouts.app', [
    'class' => 'sidebar-mini text-center',
    'namePage' => 'Add New Beneficiary',
    'activePage' => 'beneficiaries',
    'activeNav' => 'beneficiaries',
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
            <h5 class="title">{{__(" Add Beneficiary")}}</h5>
          </div>
          <div class="card-body">

                {!! Form::open(['action' => 'BeneficiariesController@store']) !!}
              @include('partials.beneficiary')
              <div class="card-footer ">
                <button type="submit" class="btn btn-info btn-round">{{__('Add New Beneficiary')}}</button>
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