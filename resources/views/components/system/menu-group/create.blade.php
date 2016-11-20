@extends('layouts.master')

@section('content')
<div class="col-md-6">
	{!! Form::open(['method' => 'POST', 'action' => 'MenuGroupController@store']) !!}
		@include('show-errors')
		<div class="form-group">
			{!! Form::label('name') !!}
			{!! Form::text('name', null, ['class' => 'form-control']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('description') !!}
			{!! Form::textarea('description', null, ['class' => 'form-control']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('visible_on') !!}
			{!! Form::text('visible_on', null, ['class' => 'form-control']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('order') !!}
			{!! Form::input('number', 'order', null, ['class' => 'form-control']) !!}
		</div>

		<div class="form-group box-body">
			@foreach(Config::get('bootstrap.fa-icons') as $icon)
				<div class="col-md-4">
					<label>
					{!! Form::radio('icon', $icon, null, ['class' => 'minimal']) !!}&nbsp;<i style="font-size: 20px;" class="fa {{ $icon }}"></i>
					&nbsp; {{ $icon }}
					</label>
				</div>
			@endforeach
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