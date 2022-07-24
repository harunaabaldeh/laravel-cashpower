
@include('alerts.success')
<div class="row">
</div>


<div class="row">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('beneficiary_id',__(" Beneficiary"), ['class'=>'text-center']) !!}
            {!! Form::select('beneficiary_id',$beneficiaries,old('beneficiary_id'),['class'=>'form-control text-center','id' =>'beneficiary_id', 'placeholder' => 'Select A Beneficiary']) !!}
            @include('alerts.feedback', ['field' => 'beneficiary_id'])
        </div>
    </div>
</div>


<div class="row pt-3">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            <div class="input-group ">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="send_currency_symbol">{{$user_country->currency_code}}</span>
                </div>
{{--                <input type="text" class="form-control" aria-label="Amount (to the nearest dollar)">--}}
                {{Form::number('source_amount',old('source_amount',null),['step' => 0.01,'min' => 1,'id' => 'source_amount','class' => 'form-control','required','placeholder' => "How much you send", 'disabled'])}}
            </div>
        </div>
    </div>
</div>

<div class="row pt-3">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            <div class="input-group ">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="destination_currency_symbol"> ____ </span>
                </div>
                {{Form::number('destination_amount',old('destination_amount',null),['step' => 0.01,'min' => 1,'id' => 'destination_amount','class' => 'form-control','required','placeholder' => "How much they receive", 'disabled'])}}
            </div>
        </div>
    </div>
</div>

{{Form::hidden("rate_id",0,['id' => 'rate_id'])}}
{{Form::hidden("type",$service_type,['id' => 'service_type'])}}
<script>
    var authUser = JSON.parse('{!! $user !!}');
    window.localStorage.setItem("x-auth-user",JSON.stringify(authUser));
</script>
