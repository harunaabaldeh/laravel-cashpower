
@include('alerts.success')
<div class="row">
</div>


<div class="row">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
{{--            <label class="text-center">{{__("Beneficiary Country")}}</label>--}}
            {!! Form::label('country_id',__(" Beneficiary Country"), ['class'=>'text-center']) !!}
            {!! Form::select('country_id',$countries,(isset($beneficiary) ? $beneficiary->country_id : null),['class'=>'form-control text-center','id' =>'country_id', 'placeholder' => 'Select Beneficiary Country']) !!}
            @include('alerts.feedback', ['field' => 'nickname'])
        </div>
    </div>
</div>

<div class="row">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
{{--            <label class="text-center">{{__("Service Type")}}</label>--}}
            {!! Form::label('service_type',__(" Service Type"), ['class'=>'text-center']) !!}
            {!! Form::select('service_type',["Bank" => "Bank", "Wallet" => "Mobile Money", "Pickup" => "Cash Pick Up"],(isset($beneficiary) ? $beneficiary->account_type : null),['class'=>'form-control text-center','id' =>'service_type', 'placeholder' => 'Select Service Type']) !!}
            @include('alerts.feedback', ['field' => 'service_type'])
        </div>
    </div>
</div>


<div class="row">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">

            {!! Form::label('firstname',__(" First Name"), ['class'=>'text-center']) !!}
            {!! Form::text('firstname',old('firstname',null),['class' => 'form-control']) !!}
            @include('alerts.feedback', ['field' => 'firstname'])
        </div>
    </div>
</div>

<div class="row">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('lastname',__(" Last Name"), ['class'=>'text-center']) !!}
            {!! Form::text('lastname',old('lastname',null),['class' => 'form-control']) !!}
            @include('alerts.feedback', ['field' => 'lastname'])
        </div>
    </div>
</div>
<div class="row">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('othernames',__("  Other Name(s)"), ['class'=>'text-center']) !!}
            {!! Form::text('othernames',old('othernames',null),['class' => 'form-control']) !!}
            @include('alerts.feedback', ['field' => 'othernames'])
        </div>
    </div>
</div>

<div class="row">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('nickname',__(" Nickname e.g Albert Mobile Money"), ['class'=>'text-center']) !!}
            {!! Form::text('nickname',old('nickname',null),['class' => 'form-control']) !!}
            @include('alerts.feedback', ['field' => 'nickname'])
        </div>
    </div>
</div>

@if(isset($beneficiary) && ($beneficiary->account_type == "PickUp" || $beneficiary->account_type == "Wallet"))
<script>
    window.localStorage.setItem("x-default-msisdn",{{$beneficiary->account_number}});
</script>
@endif

<div class="row user_mobile_wallet_row" >

    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('msisdn',__("Beneficiary Mobile Wallet/Number"), ['class'=>'text-center']) !!}
            <div class="input-group">
{{--                {!! Form::tel('msisdn[user]',old('msisdn[user]',() ? $beneficiary->account_number : null),['class' => 'form-control','id' => 'msisdn']) !!}--}}
                {!! Form::tel('msisdn[user]',old('msisdn[user]',null),['class' => 'form-control','id' => 'msisdn', 'required']) !!}
            </div>


            @include('alerts.feedback', ['field' => 'msisdn'])
        </div>
    </div>
</div>


<div class="row user_banks_row {{((isset($beneficiary) && ($beneficiary->account_type == "Bank")) ? "x-wallet-type" : "") }}">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('account_number',__(" Account Number"), ['class'=>'text-center']) !!}
            {!! Form::number('account_number',old('account_number',null),['class' => 'form-control']) !!}
            @include('alerts.feedback', ['field' => 'account_number'])
        </div>
    </div>
</div>


<div class="row user_banks_row {{((isset($beneficiary) && ($beneficiary->account_type == "Bank")) ? "x-wallet-type" : "") }} " id="user_banks_row">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">

            {!! Form::label('banks',__(" Beneficiary Bank"), ['class'=>'text-center']) !!}
            {!! Form::select('banks',isset($banks) ? $banks : [],(isset($beneficiaryBank) ? $beneficiaryBank->id : null),['class'=>'form-control text-center','id' =>'banks', 'placeholder' => 'Select Destination Bank']) !!}

            @include('alerts.feedback', ['field' => 'nickname'])
        </div>
    </div>
</div>
