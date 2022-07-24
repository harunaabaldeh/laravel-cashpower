
@include('alerts.success')
<div class="row">
</div>


<div class="row">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('source_currency',__(" Source Currency"), ['class'=>'text-center']) !!}
            {!! Form::select('source_currency',$currencies,old('source_currency'),['class'=>'form-control text-center','id' =>'source_currency', 'placeholder' => 'Select Source Currency']) !!}
            @include('alerts.feedback', ['field' => 'source_currency'])
        </div>
    </div>
</div>


<div class="row">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('destination_currency',__(" Destination Currency"), ['class'=>'text-center']) !!}
            {!! Form::select('destination_currency',$currencies,old('destination_currency'),['class'=>'form-control text-center','id' =>'destination_currency', 'placeholder' => 'Select Destination Currency']) !!}
            @include('alerts.feedback', ['field' => 'destination_currency'])
        </div>
    </div>
</div>


<div class="row pt-3">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('markup_fixed',__(" Fixed Markup"), ['class'=>'text-center']) !!}
            {{Form::number('markup_fixed',old('markup_fixed',null),['step' => 0.01,'id' => 'markup_fixed','class' => 'form-control','required','placeholder' => "Fixed Mark Up"])}}
        </div>
    </div>
</div>


<div class="row pt-3">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('markup_percentage',__(" Percentage Markup"), ['class'=>'text-center']) !!}
            {{Form::number('markup_percentage',old('markup_percentage',null),['step' => 0.01,'id' => 'markup_percentage','class' => 'form-control','required','placeholder' => "Percentage Mark Up"])}}
        </div>
    </div>
</div>

{{--{{Form::hidden("rate_id",0,['id' => 'rate_id'])}}--}}
{{--{{Form::hidden("type",$service_type,['id' => 'service_type'])}}--}}

