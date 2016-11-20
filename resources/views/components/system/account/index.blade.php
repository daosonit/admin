@extends('layouts.master')

@section('title')
	Danh sách Account
@endsection

@section('js')
    <script type="text/javascript">
        $('a.rs-pwd').click(function(event){
            return confirm('Confirm Action?');
        });
    </script>
@endsection

@section('content')
	<div class="row">
        <div class="col-xs-12">
            <div class="box">
            	{!!  Form::open(['method' => 'GET', 'name' => 'search']) !!}
                <div class="box-header">
                    <h3 class="box-title">Danh sách Account</h3>
                </div>
                <div class="box-header">
                    <div class="col-md-2">
                         <div class="input-group input-group-sm">
                            {!! Form::select('department_id', mytour_collect($departments->toArray())->prependItem('--Phòng ban--', 0)->toArray(), Input::get('department_id'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                   <div class="col-md-1">
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
                            <th>Department</th>
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
                                <td>{{ $admin->department->name }}</td>
                                <td><a title="Attach role to {{ $admin->name }} user" href="{{ route('attach-role', $admin->id) }}" class="btn btn-primary"><i class="text-large glyphicon glyphicon-check"></i></a> | <a href="{{ route('admin-edit', $admin->id) }}" class="btn btn-primary"><i class="text-large glyphicon glyphicon-edit"></i></a> | <a href="{{ route('admin-delete', $admin->id) }}" class="btn btn-default"><i class="text-red glyphicon glyphicon-trash"></i></a>
                                <a onclick="return confirm('Confirm Action')" href="{{ route('account.fake-login', ['userID' => $admin->id]) }}">Login</a> | 
                                <a class="rs-pwd" href="{{ route('account.send-password', ['userID' => $admin->id]) }}">SendPassword</a>    
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
            {!! $adminUsers->appends(['table_search' => Input::get('table_search')])->render() !!}
        </div>
    </div>
@endsection