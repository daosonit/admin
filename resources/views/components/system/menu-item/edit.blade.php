@extends('layouts.master')

@section('content')
<div class="col-md-6">
	{!! Form::model($menuItem, ['method' => 'PUT', 'route' => ['system.menu-items.update', $menuItem->id]]) !!}
		@include('show-errors')
		<div class="form-group">
			{!! Form::label('name') !!}
			{!! Form::text('name', null, ['class' => 'form-control']) !!}
		</div>
		
		<div class="form-group">
			{!! Form::label('Menu') !!}
			{!! Form::select('menu_id', $menus, $menuItem->menu_id, ['class' => 'form-control']) !!}
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
			{!! Form::checkbox('active', 1, null, ['class' => 'minimal']) !!} &nbsp;
			{!! Form::label('active') !!}
		</div>

		<div class="form-group">
			{!! Form::submit('Save',['class' => 'form-control btn btn-primary']) !!} &nbsp;
		</div>
	{!! Form::close() !!}
</div>
@stop