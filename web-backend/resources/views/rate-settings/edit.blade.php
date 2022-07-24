@extends('layouts.app', [
    'class' => 'sidebar-mini text-center',
    'namePage' => 'Update Rate Configuration',
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
            <h5 class="title">{{__(" Update Rate Configuration")}}</h5>
          </div>
          <div class="card-body">
              {!! Form::model($rateSetting, ['route' => ['rate-settings.update',$rateSetting->id],'method' => 'patch']) !!}
                @include('partials.rate-settings',['currencies' => $currencies, 'rateSetting' => $rateSetting])
              <div class="card-footer ">
                <button type="submit" class="btn btn-info btn-round">{{__('Edit Rate Configiration Detail')}}</button>
              </div>
              <hr class="half-rule"/>
            {!! Form::close() !!}
          </div>

      </div>
    </div>
    </div>
  </div>
@endsection
