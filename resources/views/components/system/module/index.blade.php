@extends('layouts.master')

@section('title')
	Module list
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
                    <h3 class="box-title">Danh sách module</h3>
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
                            <th>Action</th>
                        </tr>
                        @if(!empty($modules))
	                        @foreach($modules as $module)
	                        <tr>
	                            <td>{{ $module->mod_id }}</td>
	                            <td><span class="label label-success">{{ $module->mod_name }}</span></td>
	                            <td>
	                            	<a title="Edit {{ $module->mod_name }} role" href="{{ route('module-edit', $module->mod_id) }}"><i class="text-large glyphicon glyphicon-edit"></i></a> | 
	                            	<a title="Remove {{ $module->mod_name }} role" href="{{ route('module-delete', $module->mod_id) }}"><i class="text-large text-red glyphicon glyphicon-remove"></i></a>
	                            	
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
            {!! $modules->appends(['table_search' => Input::get('table_search')])->render() !!}
        </div>
    </div>
@endsection