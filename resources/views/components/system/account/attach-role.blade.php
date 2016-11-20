@extends('layouts.master')

@section('title')
	Attach Permission 
@endsection

@section('csrf-token')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('js')
<script type="text/javascript">
	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

	$('input.permission_check').on('ifClicked', function (event) { 
		var url = '{{ route("attach-one-role", $adminUser->id) }}';
		var $input = $(this);
		var permID = $input.val();
		$.ajax({
			url: url,
			type: 'POST',
			data: {id: permID},
			success: function(data){
				if(data.res){
					$input.parent().attr('class', '').addClass('icheckbox_square-green permission_check');
				} else {
					$input.parent().attr('class', '').addClass('icheckbox_square-red permission_check');
				}
				if(data.checked){
					$input.parent().addClass('checked');
				}
			},
			complete: function(){

			}
		});
		
	});


</script>
@endsection

@section('content')
<div class="col-md-12">
    <form method="POST" action="">
    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="box">
            <div class="box-header">
              	<h3 class="box-title">Attach Role to <span class="label label-success">{{ $adminUser->name }}</span> user</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">

              	<table id="example1" class="table table-bordered table-hover dataTable">
	                <thead>
		                <tr>
							<th>ID</th>
							<th>Name</th>
							<th>Display Name</th>
							<th>Description</th>
							<th>Attach</th>
							<th>Attach permission</th>
		                </tr>
	                </thead>
	                <tbody>
	               	@if(!empty($roles))
				    	@foreach($roles as $role)
		                <tr>
			                <td>{{ $role->id }}</td>
			                <td><span class="label label-success">{{ $role->name }}</span></td>
			                <td>{{ $role->display_name }}</td>
			                <td>{{ $role->description }}</td>
			                <td>
			                	{!! Form::checkbox('permission_id[]', $role->id, $adminUser->roles()->get()->contains($role), ['class' => 'minimal permission_check']); !!}
						    </td>
						    <td>
						    	<a title="Attach permissions to {{ $role->name }} role" href="{{ route('attach-permission', $role->id) }}"><i class="text-large glyphicon glyphicon-check">&nbsp;<strong>{{ $role->name }}</strong></i></a>
						    </td>
						</tr>
	                    @endforeach
		            @endif
	                </tbody>
	                <tfoot>
	                <tr>
	                  <th>ID</th>
	                  <th>Name</th>
	                  <th>Display Name</th>
	                  <th>Description</th>
	                  <th>Attach</th>
	                  <th>Attach permission</th>
	                </tr>
	                </tfoot>
              	</table>
            </div>
            <!-- /.box-body -->
        </div>
    </form>
</div>
@endsection

@section('js')

@endsection