@extends('layouts.app', [
    'class' => 'sidebar-mini text-center',
    'namePage' => 'Update Rate',
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
            <h5 class="title">{{__(" Update System Rate")}}</h5>
          </div>
          <div class="card-body">
              {!! Form::model($rate, ['route' => ['rates.update',$rate->id],'method' => 'patch']) !!}
                @include('partials.rates',['rate' => $rate])
              <div class="card-footer ">
                <button type="submit" class="btn btn-info btn-round">{{__('Edit Rate Detail')}}</button>
              </div>
              <hr class="half-rule"/>
            {!! Form::close() !!}
          </div>

      </div>
    </div>
    </div>
  </div>
@endsection
