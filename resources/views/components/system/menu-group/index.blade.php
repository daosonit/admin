@extends('layouts.master')

@section('js')
<script type="text/javascript">
	
	$('button.remove').click(function(event){
		return confirm('Bạn muốn xóa bản ghi ???');
	});
	$('#menu-group-table').DataTable(

	);

</script>
@stop

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="box">
			<div class="box-header">
				<h3 class="box-title">Data Table With Full Features</h3>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<table id="menu-group-table" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Description</th>
						<th>Order</th>
						<th>Visible on roles</th>
						<th>Action</th>
						
					</tr>
				</thead>
				<tbody>
					@forelse($menuGroups as $menuGroup)
					<tr>
						<td>{{ $menuGroup->id }}</td>
						<td>{{ $menuGroup->name }}</td>
						<td>{{ $menuGroup->description }}</td>
						<td>{{ $menuGroup->order }}</td>
						<td>{{ $menuGroup->visible_on }}</td>
						<td>
							<a class="btn btn-primary btn-sm pull-left" href="{{ route('system.menu-groups.edit', $menuGroup->id) }}"><span class="glyphicon glyphicon-edit"></span></a>&nbsp;

							{!! Form::open(['method' => 'DELETE', 'url' => route('system.menu-groups.destroy', $menuGroup->id), 'class' => 'pull-right']) !!}
								<button class="btn btn-danger btn-sm remove" type="submit"><span class="glyphicon glyphicon-trash"></span></button>
							{!! Form::close() !!}
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="6">No Records</td>
					</tr>
					@endforelse
				</tbody>
				<tfoot>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Description</th>
						<th>Order</th>
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