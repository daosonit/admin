@extends('layouts.master')

@section('title')
	Danh sách khách sạn
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
                    <h3 class="box-title">Danh sách khách sạn</h3>
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
                            <th style="width: 135px">Action</th>
                        </tr>
                        @if($hotels->count())
	                        @foreach($hotels->all() as $hotel)
		                        <tr>
		                        	<td>{{ $hotel->stt }}</td>
		                            <td>{{ $hotel->hot_id }}</td>
		                            <td><a href="{{ $hotel->getUrl() }}" ><strong>{{ $hotel->hot_name_temp }}</strong></a></td>
		                            <td><span class="label label-success">{{ $hotel->hot_phone }}</span></td>
		                            <td><span class="label label-success">{{ $hotel->hot_email }}</span></td>
		                            <td>{{ $hotel->hot_address_temp }}</td>
                                    <td>
                                        <p><a href="{{ route('rate-plan-list', $hotel->hot_id) }}">Quản lý đơn giá</a></p>
                                        <p><a href="{{ route('hotel-promo-list', $hotel->hot_id) }}">Quản lý khuyến mãi</a></p>
                                        <p><a href="{{ route('room-price-show', $hotel->hot_id) }}">Quản lý giá</a></p>
                                        <p><a href="{{ route('room-list', $hotel->hot_id) }}">Quản lý phòng</a></p>
                                    </td>
		                            <!-- <td>
		                            	<a href="{{ route('modules.hotels.edit', $hotel->hot_id) }}" class="btn btn-primary"><i class="text-large glyphicon glyphicon-edit"></i></a> |
		                            	<a href="{{ route('rate-plan-list', $hotel->hot_id) }}" class="btn btn-primary"><i class="text-large glyphicon glyphicon-edit"></i></a> |
		                            	<!-- {!! Form::open(['method' => 'DELETE', 'url' => route('modules.hotels.destroy', $hotel->hot_id), 'class' => 'pull-right']) !!} -->
											<!-- <button class="btn btn-danger btn-sm remove" type="submit"><span class="glyphicon glyphicon-trash"></span></button> -->
										<!-- {!! Form::close() !!} -->
		                            <!-- </td> -->
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