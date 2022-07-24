@extends('layouts.app', [
    'namePage' => 'Beneficiaries List',
    'class' => 'sidebar-mini',
    'activePage' => 'beneficiaries',
  ])

@section('content')
  <div class="panel-header panel-header-sm">
  </div>
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title"> Beneficiary Detail</h4>
          </div>
          <div class="card-body">

              <div class="offset-2 col-md-8">
                  <div class="card card-user">
                      <div class="image">
                          <img src="../assets/img/bg5.jpg" alt="...">
                      </div>
                      <div class="card-body">
                          <div class="author">
                              <a href="#">
                                  <img class="avatar border-gray" src="../assets/img/default-avatar.png" alt="...">
                                  <h5 class="title">{{$beneficiary->fullname}}</h5>
                              </a>
                              <p class="description">
                                 <strong >Nick Name:</strong> {{$beneficiary->nickname}}
                              </p>
                          </div>
                          <p class="description text-center">
                          <hr>

                          <div class="text-center">
                              <strong >Account Type:</strong> {{$beneficiary->account_type}}<br>

                              <strong >Country:</strong> {{$beneficiary->country->name}}<br>

                              @if($beneficiary->account_type == "Bank")
                                  <strong >Account Number:</strong> {{$beneficiary->account_number}}<br>
                                  <strong >Bank Name:</strong> {{$beneficiary->bank_name}}<br>
                              @endif

                              <strong >Mobile Number:</strong> {{$beneficiary->msisdn}}<br>
                          </div>

                          </p>
                      </div>
                      <hr>
                      <div class="button-container">
                          <button href="{{route('beneficiaries.edit',$beneficiary)}}" class="btn btn-neutral btn-icon btn-round btn-lg" data-toggle="tooltip" data-original-title="Edit Beneficiary Detail">
                              <i class="fa fa-edit text-info m-r-10"></i>
                          </button>
                          <button href="{{route('beneficiaries.destroy',[$beneficiary->id])}}" class="btn btn-neutral btn-icon btn-round btn-lg deleteModel" form-alert-message="Kindly Confirm the removal of this Beneficiary" form-id="beneficiariesDelete{{$beneficiary->id}}" data-toggle="tooltip" data-original-title="Remove Beneficiary">
                              <i class="fa fa-trash text-danger m-r-10"></i>
                          </button>
                          {!! Form::open(['action' => ['BeneficiariesController@destroy',$beneficiary->id], 'method' => 'DELETE', 'id' => "beneficiariesDelete$beneficiary->id","hidden" => "hidden"]); !!}
                          {!! Form::close() !!}

                          <button href="#" class="btn btn-neutral btn-icon btn-round btn-lg" data-toggle="tooltip" data-original-title="Number Of Transactions">
                              <strong>{{$beneficiary->transactions()->count()}} </strong>
                          </button>
                      </div>
                  </div>

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
                      <h4 class="card-title"> Latest Transactions To Beneficiary</h4>
                  </div>
                  <div class="card-body">
                      <div class="table-responsive">
                          <table class="table">
                              <thead class=" text-primary text-center">
                              <th>
                                  Transaction #
                              </th>
                              <th>
                                  Type
                              </th>
                              <th>
                                 Source Amount
                              </th>
                              <th>
                                 Destination Amount
                              </th>
                              <th class="text-right">
                                  Date Time
                              </th>
                              </thead>
                              <tbody>
                              @foreach($beneficiary->transactions as $transaction)
                                <tr class="text-center">
                                  <td>
                                     {{$transaction->id}}
                                  </td>
                                    <td>
                                     {{$transaction->type}}
                                  </td>
                                  <td>
                                     {{$transaction->source_currency." ".$transaction->source_amount}}
                                  </td>
                                    <td>
                                     {{$transaction->destination_currency." ".$transaction->destination_amount}}
                                  </td>

                                  <td class="text-right">
                                      {{$transaction->created_at}}
                                  </td>
                              </tr>
                              @endforeach
                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>
          </div>
      </div>
@endsection

