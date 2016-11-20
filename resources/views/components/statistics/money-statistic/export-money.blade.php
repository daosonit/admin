@extends('layouts.master')

@section('content')
<div class="col-xs-6">
	<div class="box">
		<div class="box-header">
			<h3>Export Money</h3>
		</div>

		<div class="box-body">
			{!! Form::open(['method' => 'POST', 'action' => 'Statistics\MoneyStatisticController@exportMoney']) !!}
			<div class="form-group">
				{!! Form::label('module', 'Chọn Module') !!}
				{!! Form::select('module', MytourStatic::bookingModules()->map(function($module){ 
					return $module = $module['name'];
				})->toArray(), null, ['class' => 'form-control']) !!}
			</div>

			<div class="form-group">
				{!! Form::submit('Export', ['class' => 'btn btn-primary']) !!}
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@stop