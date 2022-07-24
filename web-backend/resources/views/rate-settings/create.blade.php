@extends('layouts.app', [
    'class' => 'sidebar-mini text-center',
    'namePage' => 'Add New Rate Configurations',
    'activePage' => 'rate-settings',
    'activeNav' => 'rate-settings',
])

@section('content')
  <div class="panel-header panel-header-sm">
  </div>
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h5 class="title">{{__(" Create New Rate Configuration")}}</h5>
          </div>
          <div class="card-body">

               {!! Form::open(['action' => 'RatesSettingsController@store']) !!}
                   @include('partials.rate-settings',['currencies' => $currencies])
                     <div class="card-footer ">
                       <button type="submit" class="btn btn-info btn-round">{{__('Create Rate Configuration')}}</button>
                     </div>
                     <hr class="half-rule"/>
              {!! Form::close() !!}
          </div>

      </div>
    </div>
    </div>
  </div>
@endsection

