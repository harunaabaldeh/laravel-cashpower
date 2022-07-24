@extends('layouts.app', [
    'namePage' => $user_currency.'/USD Rates History',
    'class' => 'login-page sidebar-mini ',
    'activePage' => 'home',
    'backgroundImage' => asset('now') . "/img/bg14.jpg",
])

@section('content')
  <div class="panel-header panel-header-lg">
    <canvas id="bigDashboardChart"></canvas>
  </div>
  <div class="content">
    <div class="row">
      <div class="col-lg-6">
        <div class="card card-chart">
          <div class="card-header">
            <h5 class="card-category">Transaction Growth</h5>
{{--            <h4 class="card-title">Transaction Growth Trajectory</h4>--}}
          </div>
          <div class="card-body">
            <div class="chart-area">
              <canvas id="lineChartExample"></canvas>
            </div>
          </div>
          <div class="card-footer">
            <div class="stats">
              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
            </div>
          </div>
        </div>
      </div>

{{--      <div class="col-lg-4 col-md-6">--}}
{{--        <div class="card card-chart">--}}
{{--          <div class="card-header">--}}
{{--            <h5 class="card-category">Top Up History</h5>--}}
{{--            <h4 class="card-title">All Top Ups</h4>--}}
{{--            <div class="dropdown">--}}
{{--              <button type="button" class="btn btn-round btn-outline-default dropdown-toggle btn-simple btn-icon no-caret" data-toggle="dropdown">--}}
{{--                <i class="now-ui-icons loader_gear"></i>--}}
{{--              </button>--}}
{{--              <div class="dropdown-menu dropdown-menu-right">--}}
{{--                <a class="dropdown-item" href="#">Action</a>--}}
{{--                <a class="dropdown-item" href="#">Another action</a>--}}
{{--                <a class="dropdown-item" href="#">Something else here</a>--}}
{{--                <a class="dropdown-item text-danger" href="#">Remove Data</a>--}}
{{--              </div>--}}
{{--            </div>--}}
{{--          </div>--}}
{{--          <div class="card-body">--}}
{{--            <div class="chart-area">--}}
{{--              <canvas id="lineChartExampleWithNumbersAndGrid"></canvas>--}}
{{--            </div>--}}
{{--          </div>--}}
{{--          <div class="card-footer">--}}
{{--            <div class="stats">--}}
{{--              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated--}}
{{--            </div>--}}
{{--          </div>--}}
{{--        </div>--}}
{{--      </div>--}}
      <div class="col-lg-6 col-md-6">
        <div class="card card-chart">
          <div class="card-header">
            <h5 class="card-category">Send Distribution Per Currency</h5>
{{--            <h4 class="card-title">Fund Movements</h4>--}}
          </div>
          <div class="card-body">
            <div class="chart-area">
              <canvas id="barChartSimpleGradientsNumbers"></canvas>
            </div>
          </div>
          <div class="card-footer">
            <div class="stats">
              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h5 class="card-category">Latest Transactions</h5>
            <h4 class="card-title"> Recent Account Transactions</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              @if(isset($transactions))
                <table class="table">
                <thead class=" text-primary">
                  <th>
                    Type
                  </th>
                  <th>
                    Send Amount
                  </th>
                  <th>
                    Receive Amount
                  </th>
                  <th>
                    Beneficiary
                  </th>
                  <th class="text-right">
                    Date Time
                  </th>
                </thead>
                <tbody>

                @foreach($transactions as $transaction)
                  <tr>
                    <td>
                      {{$transaction->type}}
                    </td>

                    <td>
                      {{$transaction->source_currency." ".$transaction->source_amount }}
                    </td>

                    <td>
                      {{$transaction->destination_currency." ".$transaction->destination_amount }}
                    </td>

                    <td>
                        {{!empty($transaction->beneficiary) ? $transaction->beneficiary->fullname : "None"}}
                    </td>
                    <td class="text-right">
                        {{$transaction->created_at}}
                    </td>
                  </tr>
                @endforeach
                </tbody>
              </table>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js')
  <script>
    $(document).ready(function() {
      // Javascript method's body can be found in assets/js/demos.js

      var user_currency = "{!! $user_currency !!}";
      var rates = {!! json_encode($rates) !!};
      var RateDates = {!! json_encode($dates) !!};
      var transactionVolumes = {!! json_encode($transactionVolumeArray) !!};
      var transactionVolumeDates = {!! json_encode($transactionVolumeDates) !!};
      var receiveAmountDistributions = {!! json_encode($receiveAmountDistributions) !!};
      var uniqueUserTransactionalCurrencies = {!! json_encode($uniqueUserTransactionalCurrencies) !!};

      demo.initDashboardPageCharts(user_currency, rates, RateDates,transactionVolumes,transactionVolumeDates, receiveAmountDistributions, uniqueUserTransactionalCurrencies);


      console.log("rates", rates);
      console.log("dates", RateDates);
    });
  </script>
@endsection
