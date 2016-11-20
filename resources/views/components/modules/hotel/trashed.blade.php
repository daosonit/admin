@extends('layouts.master')

@section('title')
	Danh sách khách sạn đã xóa
@endsection

@section('js')
<script type="text/javascript">
	$('button.remove').click(function(event){
		return confirm('Bạn có muốn xóa khách sạn này ko ? ');
	});
</script>
@stop


@section('content')
	<!-- /.row -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
            	{!!  Form::open(['method' => 'GET', 'name' => 'search']) !!}
                <div class="box-header">
                    <h3 class="box-title">Danh sách khách sạn đã xóa</h3>
                </div>
                <div class="box-body" >
                    <div class="box-tools pull-left">
                        <div class="input-group input-group-sm">
                        {!! Form::select('star',$starRates->toArray() , Request::get('star'), ['class' => 'form-control', 'placeholder' => 'Số ĐT']) !!}
                        </div>
                    </div>
                	<div class="box-tools pull-left">
                    	<div class="input-group input-group-sm">
                    		{!! Form::text('id', Request::get('id'), ['class' => 'form-control', 'placeholder' => 'ID']) !!}
                    	</div>
                    </div>
                    <div class="box-tools pull-left">
                    	<div class="input-group input-group-sm">
                    		{!! Form::text('email', Request::get('email'), ['class' => 'form-control', 'placeholder' => 'Email']) !!}
                    	</div>
                    </div>
                    <div class="box-tools pull-left">
                    	<div class="input-group input-group-sm">
                    		{!! Form::text('phone', Request::get('phone'), ['class' => 'form-control', 'placeholder' => 'Số ĐT']) !!}
                    	</div>
                    </div>
                    <div class="box-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Tên KS']) !!}
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                {!!  Form::close() !!}
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tr>
                            <th>STT</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                        @if($hotels->count())
	                        @foreach($hotels->all() as $hotel)
		                        <tr>
                                    <td>{{ $hotel->stt }}</td>
		                            <td>{{ $hotel->hot_id }}</td>
		                            <td><span>{{ $hotel->hot_name_temp }}</span></td>
		                            <td><span class="label label-success">{{ $hotel->hot_phone }}</span></td>
		                            <td><span class="label label-success">{{ $hotel->hot_email }}</span></td>
		                            <td>{{ $hotel->hot_address_temp }}</td>
		                            <td>
		                            	<a href="{{ route('modules.hotels.restore', $hotel->hot_id) }}" class="btn btn-primary"><i class="text-large fa fa-undo"></i></a> 
		                            </td>
		                        </tr>
	                        @endforeach
	                    @endif
	                    <tr>
                            <th>STT</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
            {!! urldecode($hotels->appends(Input::query())->render()) !!}
        </div>
    </div>
@endsection