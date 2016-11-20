@extends('layouts.master')

@section('css')
    <style type="text/css">
        .cont ul{padding-left: 0px;list-style: none;}
        .box-title{padding-top: 7px;font-weight: bold;}
        .btn-app{height: 30px;margin-left: 0px;padding-top: 9px;margin-top: 10px;}
        .btn-app>.fa{font-size: 12px;}
    </style>
@stop

@section('title')
	list rate plan
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
    	<div class="box box-info">
            {!! Form::open(['name' => 'listRatePlan', 'method' => 'POST', 'class' => '']) !!}
            <div class="box-header">
                <h3 class="box-title col-sm-2">Tóm tắt hệ thống giá</h3>
                @if($hotel_type == 0)
                <div class="col-sm-2">
                    <a href="{{ route('rate-plan-create', array('id' => $hotel_id)) }}"><button type="button" class="btn btn-default btn-block ">Thêm mới đơn giá</button></a>
                </div>
                @endif
                <div class="col-sm-2" style="margin-top: 5px;">
                    <a href="{{ route('room-price-show', array('id' => $hotel_id)) }}">Cập nhật giá</a>
                </div>
            </div>
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover cont">
                            <tr>
                                <th></th>
                                <th>Đơn giá</th>
                                <th>Loại phòng</th>
                                <th>Điều kiện</th>
                                <th>Phụ thu</th>
                                <th></th>
                            </tr>
                            @foreach($data as $info_rate)
                                <tr id="record_{{ $info_rate['rate_id'] }}">
                                    <td></td>
                                    <td>
                                        <ul>
                                            <li style="font-weight: bold;text-transform: capitalize;">{{ $info_rate['rate_name'] }}</li>
                                            <li>{{ $info_rate['rate_type'] }}</li>
                                        </ul>
                                    </td>
                                    <td>
                                        <ul>
                                        @foreach($info_rate['rate_room'] as $room)
                                            <li>{{ $room['rom_name'] }}</li>
                                        @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        {{ $info_rate['rate_policy'] }} <br/>
                                        @if(!empty($info_rate['rate_service']))
                                            Gồm: <br/>
                                            <ul>
                                            @foreach($info_rate['rate_service'] as $service)
                                                <li>- {{ $service }}</li>
                                            @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                    <td style="font-size: 12px;">
                                        @if(isset($info_rate['rate_surcharge']['extra_bed']))
                                        {{ $info_rate['rate_surcharge']['extra_bed']['name'] }}
                                        <ul style="padding-left: 20px;list-style: square;">
                                            <li>Giá : {{ number_format(intval($info_rate['rate_surcharge']['extra_bed']['price'])) }}đ/ng/đêm</li>
                                        </ul>
                                        @endif

                                        @if(isset($info_rate['rate_surcharge']['extra_adult']))
                                        {{ $info_rate['rate_surcharge']['extra_adult']['name'] }}
                                        <ul style="padding-left: 20px;list-style: square;">
                                            <li>Tối đa : {{ $info_rate['rate_surcharge']['extra_adult']['number'] }} người</li>
                                            <li>Giá : {{ number_format(intval($info_rate['rate_surcharge']['extra_adult']['price'])) }}đ/ng/đêm</li>
                                        </ul>
                                        @endif

                                        @if(isset($info_rate['rate_surcharge']['extra_child']))
                                        {{ $info_rate['rate_surcharge']['extra_child']['name'] }}
                                        <ul style="padding-left: 20px;list-style: square;">
                                            <li>Tối đa : {{ $info_rate['rate_surcharge']['extra_child']['number'] }} người</li>
                                            @foreach($info_rate['rate_surcharge']['extra_child']['limit'] as $child)
                                                <li>{{ $child['from'] }} - {{ $child['to'] }} tuổi : {{ ($child['price'] > 0) ? number_format(intval($child['price'])) : 0 }}đ</li>
                                            @endforeach
                                            <li>{{ $info_rate['rate_surcharge']['extra_child']['child_adult'] }} tuổi trở lên : người lớn</li>
                                        </ul>
                                        @endif
                                    </td>
                                    <td>
                                        @if(($info_rate['rate_type'] == 'Đại lý (TA)' && $hotel_type != 0) || $hotel_type == 0)
                                        <ul>
                                            <li><label style="cursor: pointer;">{!! Form::checkbox('active', '0', $info_rate['rate_active'], ['class'=>'active','onclick' => 'checkActive(this)','data-id-rate' => $info_rate['rate_id']]) !!} Kích hoạt</label></li>
                                            <li>
                                                <a class="btn btn-app" href="{{ route('rate-plan-edit', array('id' => $info_rate['rate_id'])) }}">
                                                    <i class="fa fa-edit">Sửa</i> 
                                                </a>
                                            </li>
                                            <li>
                                                <a class="btn btn-app" onclick="if(confirm('Bạn muốn xóa bản ghi?')){ deleteRate(this); }" href="" data-id-rate="{{ $info_rate['rate_id'] }}">
                                                    <i class="fa fa-close">Xóa</i>
                                                </a>
                                            </li>
                                        </ul>
                                        @else
                                        <ul>
                                            <li><label style="cursor: pointer;">{!! Form::checkbox('active', '0', $info_rate['rate_active'], ['class'=>'active','onclick' => 'checkActive(this)','data-id-rate' => $info_rate['rate_id'],'disabled' => 'disabled']) !!} Kích hoạt</label></li>
                                        </ul>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        
                    </div>
                </div>
            <!-- /.box-body -->
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection

@section('js-footer')
    <script type="text/javascript">
        function checkActive(obj)
        {
            var _url = "{{ route('check-active') }}";
            var id_rate = $(obj).data('id-rate');

            $.ajax({
                type: "POST",
                url: _url,
                data: { id_rate : id_rate, _token: '{{ csrf_token() }}'},
                success: function(data) {
                    
                }
            });
        }

        function deleteRate(obj){
            var _url = "{{ route('delete-rate-plan') }}";
            var id_rate = $(obj).data('id-rate');

            $.ajax({
                type: "POST",
                url: _url,
                data: { id_rate : id_rate, _token: '{{ csrf_token() }}'},
                success: function(data) {
                    $('#record_' + id_rate).remove();
                }
            });
        }
        
    </script>

@stop