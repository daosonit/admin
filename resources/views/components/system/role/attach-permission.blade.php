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

	$('input.permission_check').on('ifChanged', function (event) { 
		var url = '{{ route("attach-one-permission", $role->id) }}';
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
              	<h3 class="box-title">Attach Permission to <span class="label label-success">{{ $role->display_name }}</span> role</h3>
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
		                </tr>
	                </thead>
	                <tbody>
	               	@if(!empty($permissions))
				    	@foreach($permissions as $permission)
		                <tr>
			                <td>{{ $permission->id }}</td>
			                <td><span class="label label-success">{{ $permission->name }}</span></td>
			                <td>{{ $permission->display_name }}</td>
			                <td>{{ $permission->description }}</td>
			                <td>
			                	<input  name="permission_id[]" {{ $role->perms()->get()->contains($permission) ? 'checked' : '' }}
						        value="{{ $permission->id }}" type="checkbox" class="minimal permission_check">
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