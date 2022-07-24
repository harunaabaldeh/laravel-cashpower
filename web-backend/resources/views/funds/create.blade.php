@extends('layouts.app', [
    'class' => 'sidebar-mini text-center',
    'namePage' => 'E-value Funding',
    'activePage' => 'funds',
    'activeNav' => 'funds',
])

@section('content')
  <div class="panel-header panel-header-sm">
  </div>
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h5 class="title">{{__(" Fund User e-value Balance")}}</h5>
          </div>
          <div class="card-body">

               {!! Form::open(['action' => ['FundsController@updateUserEValue', $user->id],'onsubmit' => 'submit.disabled = true']) !!}

            @include('alerts.success')
            <div class="row">
            </div>


            <div class="row">
              <div class="offset-2 col-md-8 pr-1">
                <div class="form-group">
                  {!! Form::label('account_name',__(" Account Name"), ['class'=>'text-center']) !!}
                  {{Form::text('account_name',$user->fullname,['id' => 'account_number',
'class' => 'form-control account_name','disabled'])}}
                  @include('alerts.feedback', ['field' => 'account_name'])
                </div>
              </div>
            </div>


            <div class="row">
              <div class="offset-2 col-md-8 pr-1">
                <div class="form-group">
                  {!! Form::label('star_account_number',__(" Star Pay Account Number"), ['class'=>'text-center']) !!}
                  {{Form::text('star_account_number',$user->star_account_number,['id' => 'star_account_number',
'class' => 'form-control','disabled'])}}
                  @include('alerts.feedback', ['field' => 'star_account_number'])
                </div>
              </div>
            </div>

            <div class="row">
              <div class="offset-2 col-md-8 pr-1">
                <div class="form-group">
                  {!! Form::label('current_balance',__(" Current Balance"), ['class'=>'text-center']) !!}
                  {{Form::text('current_balance',$current_balance,['id' => 'current_balance','class' => 'form-control',
'disabled'])}}
                  @include('alerts.feedback', ['field' => 'current_balance'])
                </div>
              </div>
            </div>


            <div class="row">
              <div class="offset-2 col-md-8 pr-1">
                <div class="form-group">
                  {!! Form::label('msisdn',__(" Mobile Number"), ['class'=>'text-center']) !!}
                  {{Form::number('msisdn',$user->msisdn,['id' => 'msisdn','class' => 'form-control','disabled'])}}
                  @include('alerts.feedback', ['field' => 'msisdn'])
                </div>
              </div>
            </div>


            <div class="row">
              <div class="offset-2 col-md-8 pr-1">
                <div class="form-group">
                  {!! Form::label('evalue',__(" Amount"), ['class'=>'text-center']) !!}
                  {{Form::number('evalue',old('evalue'),['id' => 'evalue','class' => 'form-control','step' => 0.001, 'required'])}}
                  @include('alerts.feedback', ['field' => 'evalue'])
                </div>
              </div>
            </div>


            <div class="card-footer ">
              <button type="submit" class="btn btn-info btn-round" name="submit">{{__('Top-up User E-value')}}</button>
            </div>
            <hr class="half-rule"/>
              {!! Form::close() !!}
          </div>

      </div>
    </div>
    </div>
  </div>
@endsection

