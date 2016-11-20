@extends('layouts.master')

@section('title')
	New account has been created
@stop

@section('content')
<div class="row col-md-6">
	{!! Form::open(['method' => 'GET', 'acction' => 'App\Http\Controllers\SystemController@postOutsideAccount']) !!}
	@if(Session::has('new_account'))
	<div class="alert alert-success">
		<ul>
			<li>
				Tài khoản {{ $newAccount->email }} được tạo thành công!
			</li>
		</ul>
	</div>
	@endif
	<div class="box">
		<div class="box-header">
			<h4>New Account Has Created</h4>
		</div>
		<div class="box-body">
			<div class="form-group">
				{!! Form::label('email') !!}
				{!! Form::email('email', $newAccount->email, ['readonly', 'class' => 'form-control']) !!}
			</div>
			<div class="form-group">
				{!! Form::label('password') !!}
				{!! Form::text('password', $newAccount->string_password, ['readonly', 'class' => 'form-control']) !!}
			</div>
			<div class="form-group">
				<a class="btn btn-primary" href="{{ route('account-list') }}">Done</a>
			</div>
		</div>
	</div>
	{!! Form::close() !!}
</div>
@stop