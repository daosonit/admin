@extends('layouts.master')

@section('css')
    <style type="text/css">
        .cont ul{padding-left: 0px;list-style: none;}
        .box-title{padding-top: 7px;font-weight: bold;}
        .btn-app{height: 30px;margin-left: 0px;padding-top: 9px;margin-top: 10px;}
        .btn-app>.fa{font-size: 12px;}
        ul li span{cursor: pointer; color: blue; text-decoration: underline;}
        ul li{margin-bottom: 5px;}
    </style>
@stop

@section('title')
    list rate plan
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            {!! Form::open(['name' => 'listRoom', 'method' => 'POST', 'class' => '']) !!}
            <div class="box-header">
                <h3 class="box-title col-sm-2">Danh sách phòng</h3>
                <div class="col-sm-2">
                    <a href="{{ route('room-create', array('id' => $id)) }}"><button type="button" class="btn btn-default btn-block ">Tạo mới phòng</button></a>
                </div>
            </div>
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover cont">
                            <tr>
                                <th></th>
                                <th>Loại phòng</th>
                                <th>Ảnh đại diện</th>
                                <th>Số lượng</th>
                                <th>Số người tối đa</th>
                                <th>Loại giường</th>
                                <th></th>
                            </tr>
                            @foreach($data as $info_room)
                                <tr id="record_{{ $info_room['room_id'] }}">
                                    <td></td>
                                    <td>
                                        <ul>
                                            <li style="font-weight: bold;text-transform: capitalize;margin-bottom: 5px;">{{ $info_room['name'] }}</li>
                                            @if($info_room['hot_pms_link'] != "" && $info_room['hot_pms_active'] == 1)
                                                <li>PMS tương ứng: </li>
                                                @if(in_array($info_room['rom_pms_room_id'],$info_room['pms_room_ids']))
                                                    <li class="box-show-pms_{{ $info_room['room_id'] }}" style="font-weight: bold;">{{ $info_room['pms_room_info'][$info_room['rom_pms_room_id']]['title'] }} #{{ $info_room['pms_room_info'][$info_room['rom_pms_room_id']]['ID'] }}</li>
                                                    <li class="box-show-pms_{{ $info_room['room_id'] }}" style="padding-left: 10px;">
                                                        <span class="edit-room-pms" data-room-pms-id="{{ $info_room['room_id'] }}" style="border-right: 1px solid; padding-right: 5px;margin-right: 5px;">Sửa</span>
                                                        <a href="" onclick="deleteRoomPms(this);" data-room-pms-id="{{ $info_room['room_id'] }}" style="text-decoration: underline">Xóa</a>
                                                    </li>
                                                @endif
                                                <li class="box-room-pms_{{ $info_room['room_id'] }}" {!! ($info_room['rom_pms_room_id'] == 0) ? '' : 'style="display: none;"' !!}>
                                                    <select name="rom_pms_room_id" class="pms_room_id_{{ $info_room['room_id'] }}">
                                                        <option value="0">-Chọn phòng tương ứng-</option>
                                                        @if (count($info_room['pms_room_info']) > 0 && !isset($info_room['pms_room_info']['code']))
                                                            @foreach ($info_room['pms_room_info'] as $room_pms_id => $pms_info) 
                                                                <option value="{{ $pms_info['ID'] }}" {{ ($info_room['rom_pms_room_id'] == $pms_info['ID'] ? 'selected=""' : '') }} >{{ $pms_info['title'] }} #{{ $pms_info['ID'] }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <p><a href="" onclick="saveRoomPms(this);" data-room-pms-id="{{ $info_room['room_id'] }}" style="color: blue;text-decoration: underline">Lưu</a></p>
                                                </li>
                                            @endif
                                        </ul>
                                    </td>
                                    <td>
                                        <img src="">
                                    </td>
                                    <td>
                                        {{ $info_room['number_room'] }} <br/>
                                    </td>
                                    <td>
                                        @if($info_room['person']['adult'] > 0)
                                            <p>{{ $info_room['person']['adult'] }} người lớn</p>
                                        @endif
                                        @if($info_room['person']['child'] > 0)
                                            <p>{{ $info_room['person']['child'] }} trẻ em</p>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($info_room['bed']['rom_info_bed']))
                                        <ul>
                                            @foreach($info_room['bed']['rom_info_bed'] as $info_bed)
                                            <li>{{ $info_bed }}</li>
                                            @endforeach
                                        </ul>
                                        @endif
                                        @if(isset($info_room['bed']['rom_exchange_bed']))
                                        <p style="font-style: italic;">hoặc</p>
                                        <ul>
                                            @foreach($info_room['bed']['rom_exchange_bed'] as $info_bed)
                                            <li>{{ $info_bed }}</li>
                                            @endforeach
                                        </ul>
                                        @endif
                                    </td>
                                    <td>
                                        <ul>
                                            <li><label style="cursor: pointer;">{!! Form::checkbox('active', '0', $info_room['rom_active'], ['class'=>'active','onclick' => 'checkActive(this)','data-id-room' => $info_room['room_id']]) !!} Kích hoạt</label></li>
                                            <li>
                                                <a class="btn btn-app" href="{{ route('room-edit', array('id' => $info_room['room_id'])) }}">
                                                    <i class="fa fa-edit">Sửa</i> 
                                                </a>
                                            </li>
                                            <li>
                                                <a class="btn btn-app" onclick="if(confirm('Bạn muốn xóa bản ghi?')){ deleteRoom(this); }" href="" data-id-room="{{ $info_room['room_id'] }}">
                                                    <i class="fa fa-close">Xóa</i>
                                                </a>
                                            </li>
                                        </ul>
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
            var _url = "{{ route('active-room') }}";
            var id_room = $(obj).data('id-room');

            $.ajax({
                type: "POST",
                url: _url,
                data: { id_room : id_room, _token: '{{ csrf_token() }}'},
                success: function(data) {
                    
                }
            });
        }

        function deleteRoom(obj){
            var _url = "{{ route('delete-room') }}";
            var id_room = $(obj).data('id-room');

            $.ajax({
                type: "POST",
                url: _url,
                data: { id_room : id_room, _token: '{{ csrf_token() }}'},
                success: function(data) {
                    $('#record_' + id_room).remove();
                }
            });
        }

        $('.edit-room-pms').click(function(){
            var room_pms_id = $(this).data('room-pms-id');
            $('.box-show-pms_' + room_pms_id).hide();
            $('.box-room-pms_' + room_pms_id).show();
        });

        function saveRoomPms(obj){
            var _url = "{{ route('save-room-pms') }}";
            var id_room = $(obj).data('room-pms-id');
            var id_room_pms = $('.pms_room_id_' + id_room).val();

            $.ajax({
                type: "POST",
                url: _url,
                data: { id_room : id_room, id_room_pms : id_room_pms, _token: '{{ csrf_token() }}'},
                success: function(data) {
                    location.reload();
                }
            });
        } 

        function deleteRoomPms(obj){
            var _url = "{{ route('save-room-pms') }}";
            var id_room = $(obj).data('room-pms-id');

            $.ajax({
                type: "POST",
                url: _url,
                data: { id_room : id_room, _token: '{{ csrf_token() }}'},
                success: function(data) {
                    location.reload();
                }
            });
        } 
        
    </script>
@stop