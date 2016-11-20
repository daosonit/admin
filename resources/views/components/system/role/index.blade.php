@extends('layouts.master')

@section('title')
	Role list
@endsection

@section('js')
<script type="text/javascript">
	
</script>
@endsection

@section('content')
<!-- /.row -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
            	{!!  Form::open(['method' => 'GET', 'name' => 'search']) !!}
                <div class="box-header">
                    <h3 class="box-title">Danh sách role</h3>
                    <div class="box-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" value="{{ Input::get('table_search') }}" class="form-control pull-right" placeholder="Search">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Display Name</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                        @if(!empty($roles))
	                        @foreach($roles as $role)
	                        <tr>
	                            <td>{{ $role->id }}</td>
	                            <td><span class="label label-success">{{ $role->name }}</span></td>
	                            <td>{{ $role->display_name }}</td>
	                            <td>{{ $role->description }}</td>
	                            <td>
	                            	<a title="Attach permissions to {{ $role->name }} role" href="{{ route('attach-permission', $role->id) }}"><i class="text-large glyphicon glyphicon-check"></i></a> | 
	                            	<a title="Edit {{ $role->name }} role" href="{{ route('role-edit', $role->id) }}"><i class="text-large glyphicon glyphicon-edit"></i></a> | 
	                            	<a title="Remove {{ $role->name }} role" href="{{ route('role-delete', $role->id) }}"><i class="text-large text-red glyphicon glyphicon-remove"></i></a>
	                            	
	                            </td>
	                        </tr>
	                        @endforeach
	                    @endif
                    </table>
                </div>
                {!!  Form::close() !!}
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
            {!! $roles->appends(['table_search' => Input::get('table_search')])->render() !!}
        </div>
    </div>
@endsection