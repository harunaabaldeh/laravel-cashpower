
@include('alerts.success')
<div class="row">
</div>


<div class="row pt-3">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('source_currency',__("Source Currency"), ['class'=>'text-center']) !!}
            {{Form::text('source_currency',old('source_currency',null),['id' => 'source_currency','class' => 'form-control','required','placeholder' => "Source Currency", "disabled"])}}
        </div>
    </div>
</div>

<div class="row pt-3">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('destination_currency',__("Destination Currency"), ['class'=>'text-center']) !!}
            {{Form::text('destination_currency',$rate->destination_currency,['id' => 'destination_currency','class' => 'form-control','required','placeholder' => "Destination Currency", "disabled"])}}
        </div>
    </div>
</div>



<div class="row pt-3">
    <div class="offset-2 col-md-8 pr-1">
        <div class="form-group">
            {!! Form::label('rate',__(" Rate"), ['class'=>'text-center']) !!}
            {{Form::number('rate',old('rate',null),['step' => 0.000001,'id' => 'rate','class' => 'form-control','required','placeholder' => "Exchange Rate"])}}
        </div>
    </div>
</div>

