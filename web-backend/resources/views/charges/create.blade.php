@extends('layouts.app', [
    'class' => 'sidebar-mini text-center',
    'namePage' => 'Add System Charge',
    'activePage' => 'rated',
    'activeNav' => 'rated',
])

@section('content')
  <div class="panel-header panel-header-sm">
  </div>
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h5 class="title">{{__(" Add System Charge")}}</h5>
          </div>
          <div class="card-body">
            {!! Form::open(['action' => 'ChargesController@store']) !!}
                @include('partials.charge',['countries' => $countries, 'services' => $services])
              <div class="card-footer ">
                <button type="submit" class="btn btn-info btn-round">{{__('Add Charge')}}</button>
              </div>
              <hr class="half-rule"/>
            {!! Form::close() !!}
          </div>

      </div>
    </div>
    </div>
  </div>
@endsection
