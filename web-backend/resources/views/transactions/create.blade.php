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
            <h5 class="title">{{__(" Create New Transaction")}}</h5>
          </div>
          <div class="card-body">

               {!! Form::open(['action' => 'TransactionsController@store']) !!}
                   @include('partials.transactions')
                     <div class="card-footer ">
                       <button type="submit" class="btn btn-info btn-round">{{__('Create Transaction')}}</button>
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