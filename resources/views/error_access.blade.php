@extends('layouts.master')

@section('title')
	{{ Session::get('message') }}
@endsection

@section('content')
	<div class="alert alert-danger text-center">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	@if( Session::has('message') )
        <h3>{{ Session::get('message') }}</h3>
	@else
		<h3>Error Access!</h3>
	@endif
	</div>
@endsection

