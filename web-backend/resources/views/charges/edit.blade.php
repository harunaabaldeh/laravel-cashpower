@extends('layouts.app', [
    'class' => 'sidebar-mini text-center',
    'namePage' => 'Update System Charge',
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
            <h5 class="title">{{__(" Update System Charge")}}</h5>
          </div>
          <div class="card-body">
              {!! Form::model($charge, ['route' => ['charges.update',$charge->id],'method' => 'patch']) !!}
                @include('partials.charge',['charge' => $charge,'countries' => $countries, 'services' => $services])
              <div class="card-footer ">
                <button type="submit" class="btn btn-warning btn-round">{{__('Update Charge Detail')}}</button>
              </div>
              <hr class="half-rule"/>
            {!! Form::close() !!}
          </div>

      </div>
    </div>
    </div>
  </div>
@endsection
