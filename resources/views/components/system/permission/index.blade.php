@extends('layouts.master')

@section('title')
    Permission list
@endsection

@section('content')
<!-- /.row -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
            	{!!  Form::open(['method' => 'GET', 'name' => 'search']) !!}
                <div class="box-header">
                    <h3 class="box-title">Danh sách permission</h3>
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
                        @if(!empty($permissions))
	                        @foreach($permissions as $permission)
	                        <tr>
	                            <td>{{ $permission->id }}</td>
	                            <td><span class="label label-success">{{ $permission->name }}</span></td>
	                            <td>{{ $permission->display_name }}</td>
	                            <td>{{ $permission->description }}</td>
	                            <td><a href="{{ route('permission-edit', $permission->id) }}"><i class="text-large glyphicon glyphicon-edit"></i></a></td>
	                        </tr>
	                        @endforeach
	                    @endif
                    </table>
                </div>
                {!!  Form::close() !!}
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
            {!! $permissions->appends(['table_search' => Input::get('table_search')])->render() !!}
        </div>

    </div>
@endsection