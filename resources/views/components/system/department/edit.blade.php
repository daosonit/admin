@extends('layouts.master')

@section('content')
<div class="col-md-7">
	<div class="box box-primary">
	    <div class="box-header with-border">
	        <h3 class="box-title">Create a new department</h3>
	    </div>
	    <div class="box-body">
		{!! Form::model($department, ['method' => 'PUT', 'route' => ['system.departments.update', $department->id]]) !!}
		@include('show-errors')
		<div class="form-group">
			{!! Form::label('name') !!}
			{!! Form::text('name', null, ['class' => 'form-control']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('description') !!}
			{!! Form::textarea('description', null, ['class' => 'form-control textarea']) !!}
		</div>

		<div class="form-group">
			{!! Form::submit('Save',['class' => 'form-control btn btn-primary']) !!} &nbsp;
		</div>
		{!! Form::close() !!}
	</div>	
</div>
@stop