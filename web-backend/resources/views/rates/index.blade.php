@extends('layouts.app', [
    'namePage' => 'System Rates List',
    'class' => 'sidebar-mini',
    'activePage' => 'rates.index',
  ])
@section('styles')
    <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css" rel="stylesheet">

@endsection
@section('js')
    <script  src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>

    {{$dataTable->scripts()}}
@endsection
@section('content')
  <div class="panel-header panel-header-sm">
  </div>
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title"> System Rates Listing</h4>
{{--              <small class="text-danger">Hover over a status to get further details on the transaction status</small>--}}
          </div>
          <div class="card-body">
            <div class="table-responsive">
                {{$dataTable->table()}}
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
@endsection


