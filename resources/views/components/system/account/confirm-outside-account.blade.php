@extends('layouts.master')

@section('title')
 Confirm user has outside account
@stop

@section('content')
<div class="row col-md-6">
	{!! Form::open(['method' => 'POST', 'acction' => 'App\Http\Controllers\SystemController@postOutsideAccount']) !!}
	@if(Session::has('message'))
	<div class="alert alert-danger">
		<ul>
			<li>
				{{ Session::get('message') }}	
			</li>
		</ul>
	</div>
	@endif
	<div class="box">
		<div class="box-header">
			<h4>Confirm Outside Email</h4>
		</div>
		<div class="box-body">
			<div class="form-group">
				{!! Form::label('outside_email') !!}
				{!! Form::email('outside_email', null, ['required', 'class' => 'form-control', 'placeholder' => 'Outside Email']) !!}
			</div>
			<div class="form-group">
				{!! Form::submit('Next', ['class' => 'btn btn-primary']) !!}
			</div>
		</div>
	</div>
	{!! Form::close() !!}
</div>
@stop

