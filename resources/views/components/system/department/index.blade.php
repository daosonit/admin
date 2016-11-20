@extends('layouts.master')

@section('title')
	Danh sách phòng ban
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

</script>
@stop

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="box">
			<div class="box-header">
				<h3 class="box-title">Danh sách phòng ban</h3>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<table id="menu-group-table" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Description</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@forelse($departments as $department)
					<tr>
						<td>{{ $department->id }}</td>
						<td>{{ $department->name }}</td>
						<td>{!! $department->description !!}</td>
						<td>
							<a class="btn btn-primary btn-sm" href="{{ route('system.departments.edit', $department->id) }}"><span class="glyphicon glyphicon-edit"></span></a>
							<a class="btn btn-primary btn-sm" href="{{ route('dep-attach-role', $department->id) }}"><span class="glyphicon glyphicon-check"></span></a>

							{!! Form::open(['method' => 'DELETE', 'url' => route('system.departments.destroy', $department->id), 'class' => 'pull-right']) !!}
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
						<th>Name</th>
						<th>Description</th>
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