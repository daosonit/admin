@extends('layouts.master')

@section('content')
<div class="col-md-6">
	{!! Form::open(['method' => 'POST', 'action' => 'MenuItemController@store']) !!}
		@include('show-errors')
		<div class="form-group">
			{!! Form::label('name') !!}
			{!! Form::text('name', null, ['class' => 'form-control']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('Menu') !!}
			{!! Form::select('menu_id', $menus, null, ['class' => 'form-control']) !!}
		</div>	

		<div class="form-group">
			{!! Form::label('visible_on') !!}
			{!! Form::text('visible_on', null, ['class' => 'form-control']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('route') !!}
			{!! Form::text('route', null, ['class' => 'form-control']) !!}
		</div>

		<div class="form-group">
			{!! Form::checkbox('active', 1, false, ['class' => 'minimal']) !!} &nbsp;
			{!! Form::label('active') !!}
		</div>

		<div class="form-group">
			{!! Form::submit('Save',['class' => 'form-control btn btn-primary']) !!} &nbsp;
		</div>
	{!! Form::close() !!}
</div>
@stop