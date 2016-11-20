@extends('layouts.master')

@section('title')
	Danh sách Account
@endsection

@section('content')
	<div class="row">
        <div class="col-xs-12">
            <div class="box">
            	{!!  Form::open(['method' => 'GET', 'name' => 'search']) !!}
                <div class="box-header">
                    <h3 class="box-title">Danh sách Account đã xóa</h3>
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
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                        @if(!empty($adminUsers))
	                        @foreach($adminUsers as $admin)
	                        <tr>
	                            <td>{{ $admin->id }}</td>
	                            <td><span class="label label-success">{{ $admin->name }}</span></td>
	                            <td>{{ $admin->email }}</td>
	                            <td>{{ $admin->phone }}</td>
	                            <td>{{ $admin->address }}</td>
                                <td><a title="Restore {{ $admin->name }} user" href="{{ route('admin-restore', $admin->id) }}" class="btn btn-primary"><i class="text-large fa fa-undo"></i></a> </td>
	                        </tr>
	                        @endforeach
	                    @endif
                    </table>
                </div>
                {!!  Form::close() !!}
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
            {!! $adminUsers->appends(['table_search' => Input::get('table_search')])->render() !!}
        </div>
    </div>
@endsection