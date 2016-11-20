@extends('layouts.master')



@section('content')
<div class="col-xs-12">
	<div class="box">
		<div class="box-body" >
			{!! Form::open(['method' => 'GET']) !!}
			<div class="box-tools pull-left">
            	<div class="input-group input-group-sm">
            		{!! Form::select('state', MytourStatic::bookingStates()->prependItem('--Trạng thái--', -1)->toArray(), Request::get('state'), ['class' => 'form-control']) !!}
            	</div>
            </div>
            <div class="box-tools pull-left">
            	<div class="input-group input-group-sm">
            		{!! Form::text('code', Request::get('code'), ['class' => 'form-control', 'placeholder' => 'Mã BK']) !!}
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
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
		<!-- /.box-header -->
		<div class="box-body table-responsive no-padding">
			<table class="table table-hover">
				<tbody>
					<tr>
						<th>Code</th>
						<th>Custmer Info</th>
						<th>Hotel Info</th>
						<th>Info</th>
						<th>Room Info</th>
						<th>Reason</th>
					</tr>
					@foreach($bookings as $booking)
					<tr>
						<td><span class="label label-success">{{ $booking }}</span></td>
						<td>
							<table class="table table-bordered">
								<tr>
									<td class="col-md-3">Name: </td><td>{{ $booking->getCustomerInfo()['name'] }}</td>
								</tr>
								<tr>
									<td>Phone: </td><td>{{ $booking->getCustomerInfo()['phone'] }}</td>
								</tr>
								<tr>
									<td>Email: </td><td>{{ $booking->getCustomerInfo()['email'] }}</td>
								</tr>
								<tr>
									<td>Address: </td><td>{{ $booking->getCustomerInfo()['address'] }}</td>
								</tr>
								<tr>
									<td>City: </td><td>{{ $booking->city ? $booking->city->cou_name : '' }}</td>
								</tr>
							</table>
						</td>
						<td>
							<table class="table table-bordered pull-left">
								<tr>
									<td class="col-md-4">Hotel Name: </td><td>{{ $booking->hotel->hot_name_temp }}</td>
								</tr>
								<tr>
									<td>Hotel City: </td><td>{{ ($booking->hotel->city ? $booking->hotel->city->cou_name : '') }}</td>
								</tr>
								<tr>
									<td>Hotel Address: </td><td>{{ $booking->hotel->hot_address_temp }}</td>
								</tr>
							</table>
						</td>
						<td>
							<table class="table table-bordered pull-left">
								<tr>
									<td class="col-md-3">Booked at: </td><td>{{ $booking->getDateTimeBook()->format('d/m/y - h:i:s A') }}</td>
								</tr>
								<tr>
									<td>Checkin: </td><td>{{ $booking->getDateTimeCheckin()->format('d/m/y - h:i:s A') }}</td>
								</tr>
								<tr>
									<td>Checkout: </td><td>{{ $booking->getDateTimeCheckin()->format('d/m/y - h:i:s A') }}</td>
								</tr>
							</table>
						</td>
						<td>Bacon ipsum dolor sit </td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	<!-- /.box-body -->
		<div class="box-footer clearfix">
              {!! $bookings->appends(Request::query())->render() !!}
        </div>
	</div>
<!-- /.box -->
</div>
@stop