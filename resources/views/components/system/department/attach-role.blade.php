@extends('layouts.master')

@section('js')
<script type="text/javascript">
	$('input.check_all_user').on('ifChanged', function(event){

		var roleID = $(this).val();

		if($(this).prop('checked')){
			$('input.user_role[data-role-id="'+roleID+'"]').iCheck('check');
		} else {
			$('input.user_role[data-role-id="'+roleID+'"]').iCheck('uncheck');
		}
	})

	$('h4.show-user').click(function(event){
		$(this).parent().next().slideToggle('fast');
	});

	$('input.check_all').on('ifChanged', function(){
		var userID = $(this).data('user-id');
		if($(this).prop('checked')){
			$('input.user_role[data-user-id="'+userID+'"]').iCheck('check');
		} else {
			$('input.user_role[data-user-id="'+userID+'"]').iCheck('uncheck');
		}
	});


	$('input.btn-primary[type="submit"]').click(function(event){
		return confirm('Do you want to save ?');
	});
</script>
@stop
@section('css')
<style type="text/css">
	h4.show-user {
		cursor: pointer;
	}
	h4.show-user:hover {
		color: green;
	}
</style>
@stop

@section('content')
<div class="row">
	<div class="col-md-12">
		{!! Form::open(['method' => 'POST', 'url' => route('dep-attach-role', ['depID' => $department->id]) ]) !!}
			<div class="box">
				<div class="box-header">
					<h1>Attach Role For <span class="label label-success">{{ $department->name }} </span> Department</h1>
				</div>
				<div class="box-body">
				<div class="row">
					<div class="box">
						<div class="box-header">
							<h4>All User</h4>	
							<a class="btn btn-primary" href="{{ route('system.departments.index') }}" >Back</a>
							{!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
						</div>
						<div class="box-body">
						@foreach($roleGroups as $roles)
							<div class="row">
								@foreach($roles as $role)
									<div class="col-md-3">
									{!! Form::checkbox('role_all' , $role->id,  false , ['class' => 'check_all_user minimal', 'id' => 'role_all'.$role->id]) !!}
									{!! Form::label('role_all'.$role->id, $role->display_name) !!}
									</div>
								@endforeach
							</div>
						@endforeach
						</div>
					</div>
					
				</div>
				@foreach($department->adminUsers as $adminUser)
					<div class="row">
						<div class="box">
							<div class="box-header">
								<h4 class="show-user">{{ $adminUser->name }}</h4>	
							</div>
							<div class="box-body" style="display: none;">
								<div class="row">
									<div class="col-md-3 pull-right">
										{!! Form::checkbox('check_all' , 0,  false , ['class' => 'minimal check_all', 'id' => 'check_all'.$adminUser->id, 'data-user-id' => $adminUser->id]) !!}
										{!! Form::label('check_all'.$adminUser->id, 'Check All') !!}
									</div>
								</div>
								@foreach($roleGroups as $roles)
									<div class="row">
										@foreach($roles as $role)
											<div class="col-md-3">
											{!! Form::checkbox('role['. $adminUser->id.'][]' , $role->id, $adminUser->roles->contains($role) ? true : false , ['class' => 'minimal user_role', 'id' => 'role'.$adminUser->id.$role->id, 'data-user-id' => $adminUser->id, 'data-role-id' => $role->id]) !!}
											{!! Form::label('role'.$adminUser->id.$role->id, $role->display_name) !!}
											</div>
										@endforeach
									</div>
								@endforeach
							</div>
						</div>
						
					</div>
				@endforeach
				</div>
				<div class="box-footer">
					<a class="btn btn-primary" href="{{ route('system.departments.index') }}" >Back</a>
					{!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
				</div>
			</div>
		{!! Form::close() !!}
	</div>
</div>
@stop