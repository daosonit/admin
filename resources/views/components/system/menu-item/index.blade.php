@extends('layouts.master')

@section('title')
	Danh sách menu
@stop

@section('js')
<script type="text/javascript">
	
	$('button.remove').click(function(event){
		return confirm('Bạn muốn xóa bản ghi ???');
	});
	$('#menu-group-table').DataTable({
		"paging": true,
		"ordering": true,
		"info": true,
		"autoWidth": false
	});

	// $('#menu_group_id').change(function(event){
	// 	this.form.submit();
	// });

</script>
@stop

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="box">
			<div class="box-header">
				<h3 class="box-title">Danh sách menu</h3>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<table id="menu-group-table" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>ID</th>
						<th>Menu</th>
						<th>Url</th>
						<th>Visible on roles</th>
						<th>Action</th>
						
					</tr>
				</thead>
				<tbody>
					@forelse($menuItems as $menuItem)
					<tr>
						<td>{{ $menuItem->id }}</td>
						<td class="text-bold">{{ $menuItem->menu->name }}&nbsp;&nbsp;&nbsp;=> &nbsp;<a target="_blank" href=" {{ Route::getRoutes()->hasNamedRoute($menuItem->route) ? route($menuItem->route) : '/' }}">{{ $menuItem->name }}</a></td>


						<td><a target="_blank" href=" {{ Route::getRoutes()->hasNamedRoute($menuItem->route) ? route($menuItem->route) : '/' }}">{{ Route::getRoutes()->hasNamedRoute($menuItem->route) ? route($menuItem->route) : 'Route Undefined' }}</a></td>
						<td>{{ $menuItem->visible_on }}</td>
						<td>
							<a class="btn btn-primary btn-sm pull-left" href="{{ route('system.menu-items.edit', $menuItem->id) }}"><span class="glyphicon glyphicon-edit"></span></a>&nbsp;

							{!! Form::open(['method' => 'DELETE', 'url' => route('system.menu-items.destroy', $menuItem->id), 'class' => 'pull-right']) !!}
								<button class="btn btn-danger btn-sm remove" type="submit"><span class="glyphicon glyphicon-trash"></span></button>
							{!! Form::close() !!}
						</td>
					</tr>
					@empty
					<tr>
						<td>No Records</td><td></td><td></td><td></td><td></td><td></td>

					</tr>
					@endforelse
				</tbody>
				<tfoot>
					<tr>
						<th>ID</th>
						<th>Menu</th>
						<th>Url</th>
						<th>Visible on roles</th>
						<th>Action</th>
					</tr>
				</tfoot>
				</table>
			</div>
		<!-- /.box-body -->
		</div>
	<!-- /.box -->
	</div>
	<!-- /.col -->
</div>
@stop