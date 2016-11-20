@extends('layouts.master')

@section('content')
<div class="col-md-6">
	{!! Form::model($menu, ['method' => 'PUT', 'route' => ['system.menus.update', $menu->id]]) !!}
		@include('show-errors')
		<div class="form-group">
			{!! Form::label('name') !!}
			{!! Form::text('name', null, ['class' => 'form-control']) !!}
		</div>
		
		<div class="form-group">
			{!! Form::label('Menu Group') !!}
			{!! Form::select('menu_group_id', $menuGroups, $menu->menu_group_id, ['class' => 'form-control']) !!}
		</div>		

		<div class="form-group">
			{!! Form::label('visible_on') !!}
			{!! Form::text('visible_on', null, ['class' => 'form-control']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('order') !!}
			{!! Form::input('number', 'order', null, ['class' => 'form-control']) !!}
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