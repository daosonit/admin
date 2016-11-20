@extends('layouts.master')


@section('js')
<script type="text/javascript">
	$('#checkAll').on('ifChanged', function(event){
	  if($(this).prop('checked')){
	  	$('input.role').iCheck('check');
	  } else {
	  	$('input.role').iCheck('uncheck');
	  }
	});

</script>
@stop

@section('content')
<div class="box box-primary">
	{!! Form::open(['method' => 'POST']) !!}
	<div class="box-header with-border">
		<h3 class="box-title">Initialize Role</h3>
	</div>
	<div class="box-body">
		<div class="col-md-12">
		{!! Form::checkbox('checkAll',null, null, [ 'id' => 'checkAll', 'class' => 'minimal'] ) !!}
		<label for="checkAll">Check All</label>
		</div>
	</div>
	<div class="box-body">
		@foreach($rolesList as $roleGroup => $roles)
			<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title">{{ $roleGroup }}</h3>
			</div>
			<div class="box-body">
			@foreach($roles as $role)
				<div class="col-md-3">
					{!! Form::checkbox('role[' . $role->id . ']', $role->id, null, [ 'id' => 'role[' . $role->id . ']', 'class' => 'minimal role'] ) !!}
					<label for="{{ 'role[' . $role->id . ']' }}">{{ $role->display_name }}</label>
				</div>
			@endforeach
			</div>
			</div>
		@endforeach
	</div>
	<div class="box-footer">
		<div class="col-md-12">
		{!! Form::submit('next', ['class' => 'form-button btn btn-primary']) !!}
		</div>
	</div>
	{!! Form::close() !!}
</div>
@stop