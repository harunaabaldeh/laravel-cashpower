
@include('alerts.success')
<div class="row">
</div>


<div class="row pt-3">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('account_type',__("Account Type"), ['class'=>'text-center']) !!}
            {!! Form::select('account_type',["User" => "User", "Agent" => "Agent"],old('account_type'),[
'class'=>'form-control text-center','id' =>'account_type', 'placeholder' => 'Account Type']) !!}
        </div>
    </div>
</div>

<div class="row pt-3">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('service_name',__("Service Name"), ['class'=>'text-center']) !!}
            {!! Form::select('service_name',$services,old('service_name'),[
'class'=>'form-control text-center','id' =>'service_name', 'placeholder' => 'Service Type']) !!}
        </div>
    </div>
</div>



<div class="row pt-3">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('source_country',__("Source Country"), ['class'=>'text-center']) !!}
            {!! Form::select('source_country',$countries,old('source_country'),[
    'class'=>'form-control text-center','id' =>'source_country', 'placeholder' => 'Select Source Country']) !!}
        </div>
    </div>
</div>

<div class="row pt-3">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('destination_country',__("Destination Country"), ['class'=>'text-center']) !!}
            {!! Form::select('destination_country',$countries,old('destination_country'),[
    'class'=>'form-control text-center','id' =>'destination_country',
    'placeholder' => 'Select Destination Country']) !!}
        </div>
    </div>
</div>




<div class="row pt-3">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('fixed_charge',__(" Fixed"), ['class'=>'text-center']) !!}
            {{Form::number('fixed_charge',old('fixed_charge',null),['step' => 0.000001,'id' => 'fixed_charge',
'class' => 'form-control','required','placeholder' => "Fixed Charge"])}}
        </div>
    </div>
</div>


<div class="row pt-3">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('percentage_charge',__(" Percentage"), ['class'=>'text-center']) !!}
            {{Form::number('percentage_charge',old('percentage_charge',null),['step' => 0.000001,
'id' => 'percentage_charge','class' => 'form-control','required','placeholder' => "Percentage Charge"])}}
        </div>
    </div>
</div>


