@extends('layouts.master')

@section('css')
    <style type="text/css">
        .sm-lb{font-weight: normal;font-size: 14px;}
        .bg-lb{font-size: 16px; text-transform: uppercase;}
        .content-box .form-group{margin-bottom: 40px;}
        .input-group[class*=col-] {float: left;!important; padding: 0px 15px!important;}
        .r2{width: 50px;}
        .conv{margin-left: 15px;}
    </style>
@stop

@section('title')
    Edit room
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            {!! Form::open(['name' => 'edit-room', 'method' => 'POST', 'class' => '']) !!}
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="box-header">
                <h3 class="box-title">Sửa loại phòng</h3>
            </div>
                <div class="box-body">
                                  
                    <div class="form-group">
                        <label class="bg-lb">Thông tin chung</label>
                        
                    </div>
                        <div class="box-body content-box">
                            <div class="form-group">
                                <label for="room_type" class="col-sm-3 control-label sm-lb">Loại phòng<sup><i style="font-size: 8px;color: red;" class="fa fa-fw fa-asterisk"></i></sup></label>

                                <div class="col-sm-4">
                                    {!! Form::text('room_type', $data_show['room_name'], ['class' => 'form-control']) !!}
                                    <div><i class="fa fa-fw fa-info-circle" style="color: blue;"></i><span><i style="font-size: 12px;">Khách hàng sẽ nhìn thấy tên phòng này trên website</i></span></div>
                                </div>                           
                            </div>
                            
                            <br/>
                            <div class="form-group">
                                <label for="room_total" class="col-sm-3 control-label sm-lb">Tổng số phòng hiện có<sup><i style="font-size: 8px;color: red;" class="fa fa-fw fa-asterisk"></i></sup></label>

                                <div class="col-sm-4">
                                    {!! Form::text('room_total', $data_show['room_total'], ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br/>
                            <div class="form-group">
                                <label for="room_area" class="col-sm-3 control-label sm-lb">Diện tích phòng tối thiểu<sup><i style="font-size: 8px;color: red;" class="fa fa-fw fa-asterisk"></i></sup></label>

                                <div class="col-sm-2 input-group">
                                    {!! Form::text('room_area', $data_show['room_area'], ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">m<sup>2</sup></span>
                                </div>
                            </div>
                            <br/>
                            <div class="form-group">
                                <label for="adult" class="col-sm-3 control-label sm-lb">Số người lớn tối đa<sup><i style="font-size: 8px;color: red;" class="fa fa-fw fa-asterisk"></i></sup></label>

                                <div class="col-sm-4">
                                    {!! Form::text('adult', $data_show['room_adult'], ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br/>
                            <div class="form-group">
                                <label for="chidrent" class="col-sm-3 control-label sm-lb">Số trẻ em tối đa</label>

                                <div class="col-sm-4">
                                    {!! Form::text('child', $data_show['room_child'], ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br/>
                            <div class="form-group">
                                <label for="child" class="col-sm-3 control-label sm-lb">Hút thuốc</label>

                                <div class="col-sm-4">
                                    <label>
                                        {!! Form::radio('room_smoke', '1', ($data_show['room_smoke'] == 1 ? true : false), ['class' => 'minimal-red']) !!}
                                    </label>
                                    <label class="r2">Có</label>
                                    <label>
                                        {!! Form::radio('room_smoke', '0', ($data_show['room_smoke'] == 0 ? true : false), ['class' => 'minimal-red']) !!}
                                    </label>
                                    <label class="r2">Không</label>
                                </div>
                            </div>
                        
                        </div>
                    <!-- /.box-body-1 -->
                    
                    <div class="form-group">
                        <label class="bg-lb">Loại giường<sup><i style="font-size: 8px;color: red;" class="fa fa-fw fa-asterisk"></i></sup></label>
                        
                    </div>
                        <div class="box-body content-box">
                            <div class="form-group">
                                <label for="room_type" class="col-sm-3 control-label sm-lb">Giường đơn (Single bed)</label>

                                <div class="col-sm-1">
                                    {!! Form::input('number','bed[single-bed]', $data_show['bed']['single-bed'], ['class' => 'form-control bfh-number']) !!}
                                </div>
                            </div>
                            <br/>
                            <div class="form-group">
                                <label for="room_total" class="col-sm-3 control-label sm-lb">Giường dôi (Double bed)</label>

                                <div class="col-sm-1">
                                    {!! Form::input('number','bed[double-bed]', $data_show['bed']['double-bed'], ['class' => 'form-control bfh-number']) !!}
                                </div>
                            </div>
                            <br/>
                            <div class="form-group">
                                <label for="room_area" class="col-sm-3 control-label sm-lb">Giường đôi lớn (Queen bed)</label>

                                <div class="col-sm-1">
                                    {!! Form::input('number','bed[queen-bed]', $data_show['bed']['queen-bed'], ['class' => 'form-control bfh-number']) !!}
                                </div>
                            </div>
                            <br/>
                            <div class="form-group">
                                <label for="adult" class="col-sm-3 control-label sm-lb">Giường đôi rất lớn (King bed)</label>

                                <div class="col-sm-1">
                                    {!! Form::input('number','bed[king-bed]', $data_show['bed']['king-bed'], ['class' => 'form-control bfh-number']) !!}
                                </div>
                            </div>
                            <br/>
                            <div class="form-group">
                                <label for="adult" class="col-sm-3 control-label sm-lb">Giường tầng</label>

                                <div class="col-sm-1">
                                    {!! Form::input('number','bed[division-bed]', $data_show['bed']['division-bed'], ['class' => 'form-control bfh-number']) !!}
                                </div>
                            </div>
                            <br/>
                            <div class="form-group">
                                <label for="adult" class="col-sm-3 control-label sm-lb">Giường Sofa</label>

                                <div class="col-sm-1">
                                    {!! Form::input('number','bed[sofa-bed]', $data_show['bed']['sofa-bed'], ['class' => 'form-control bfh-number']) !!}
                                </div>
                            </div>
                            <br/>
                            <label class="add-bed" style="margin-left: 5px; font-weight: normal; color: #0C59CF; cursor: pointer;">
                            Thêm Loại giường thay thế <i class="fa fa-fw fa-angle-down"></i></label>
                        
                        </div>

                        <!-- /.box them giuong thay the -->

                        <div class="box-body content-box box-ex-bed" style="display: none;">
                            <div class="form-group">
                                <label for="room_type" class="col-sm-3 control-label sm-lb">Giường đơn (Single bed)</label>

                                <div class="col-sm-1">
                                    <input type="number" class="form-control bfh-number" name="ex-single-bed" value="{!! $data_show['ex_bed']['ex-single-bed'] !!}">
                                </div>
                            </div>
                            <br/>
                            <div class="form-group">
                                <label for="room_total" class="col-sm-3 control-label sm-lb">Giường dôi (Double bed)</label>

                                <div class="col-sm-1">
                                    <input type="number" class="form-control bfh-number" name="ex-double-bed" value="{!! $data_show['ex_bed']['ex-double-bed'] !!}">
                                </div>
                            </div>
                            <br/>
                            <div class="form-group">
                                <label for="room_area" class="col-sm-3 control-label sm-lb">Giường đôi lớn (Queen bed)</label>

                                <div class="col-sm-1">
                                    <input type="number" class="form-control bfh-number" name="ex-queen-bed" value="{!! $data_show['ex_bed']['ex-queen-bed'] !!}">
                                </div>
                            </div>
                            <br/>
                            <div class="form-group">
                                <label for="adult" class="col-sm-3 control-label sm-lb">Giường đôi rất lớn (King bed)</label>

                                <div class="col-sm-1">
                                    <input type="number" class="form-control bfh-number" name="ex-king-bed" value="{!! $data_show['ex_bed']['ex-king-bed'] !!}">
                                </div>
                            </div>
                            <br/>
                            <div class="form-group">
                                <label for="adult" class="col-sm-3 control-label sm-lb">Giường tầng</label>

                                <div class="col-sm-1">
                                    <input type="number" class="form-control bfh-number" name="ex-division-bed" value="{!! $data_show['ex_bed']['ex-division-bed'] !!}">
                                </div>
                            </div>
                            <br/>
                            <div class="form-group">
                                <label for="adult" class="col-sm-3 control-label sm-lb">Giường Sofa</label>

                                <div class="col-sm-1">
                                    <input type="number" class="form-control bfh-number" name="ex-sofa-bed" value="{!! $data_show['ex_bed']['ex-sofa-bed'] !!}">
                                </div>
                            </div>
                        
                        </div>
                    <!-- /.box-body-2 -->

                    <div class="form-group">
                        <label class="bg-lb">Tiện nghi phòng<sup><i style="font-size: 8px;color: red;" class="fa fa-fw fa-asterisk"></i></sup></label>
                    </div>
                        <div class="box-body content-box">
                            <div class="form-group">
                            @foreach ($data_conveniences as $value)
                                <label class="conv col-sm-3">
                                    <input type="checkbox" class="flat-red control-label" name="convenience[]" value="{{ $value->con_id }}" {!! (in_array($value->con_id,$data_show['convenience']) ? 'checked' : '') !!}>
                                    {{ $value->con_name }}
                                </label>
                                <!-- <label class="conv">
                                    <input type="checkbox" class="flat-red col-sm-3 control-label">
                                </label>
                                <label>
                                    Quạt máy
                                </label> -->
                            @endforeach
                            </div>
                        </div>
                    <!-- /.box-body-3 -->

                    <div class="form-group">
                        <label class="bg-lb">Hướng phòng</label>
                    </div>
                        <div class="box-body content-box">
                            <div class="form-group">
                                <div>
                                    <label class="conv col-sm-2">
                                        <input type="radio" name="trend" class="minimal-red" value="1" {!! $data_show['trend'][1] !!}>
                                        Hướng biển</label>
                                    <label class="conv col-sm-2">
                                        <input type="radio" name="trend" class="minimal-red" value="2" {!! $data_show['trend'][2] !!}>
                                        Sát biển</label>
                                    <label class="conv col-sm-2">
                                        <input type="radio" name="trend" class="minimal-red" value="3" {!! $data_show['trend'][3] !!}>
                                        Hường phố</label>
                                </div>
                                <div style="clear: both; margin-bottom:10px;"></div>
                                <div>
                                    <label class="conv col-sm-2">
                                        <input type="radio" name="trend" class="minimal-red" value="4" {!! $data_show['trend'][4] !!}>
                                        Hướng vườn</label>
                                    <label class="conv col-sm-2">
                                        <input type="radio" name="trend" class="minimal-red" value="5" {!! $data_show['trend'][5] !!}>
                                        Hướng hồ bơi</label>
                                    <label class="conv" style="margin-left: 30px;">
                                        <input type="radio" name="trend" class="minimal-red" value="6" {!! $data_show['trend'][6] !!}>
                                        Hướng 1 phần (nghĩa là không hoàn toàn)</label>
                                </div>
                                <div style="clear: both; margin-bottom:10px;"></div>
                                <div>
                                    <label class="conv col-sm-2">
                                        <input type="radio" name="trend" class="minimal-red" value="0" {!! $data_show['trend'][0] !!}>
                                        Không có</label>
                                </div>
                            </div>
                        </div>
                    <!-- /.box-body-3 -->
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-block btn-primary">Submit</button>
                </div>
                <div class="col-sm-2" style="padding-top: 5px; margin-left: 30px;">
                    <a href="">Reset</a>
                </div>
            <!-- /.box-body -->
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection

@section('js-footer')
    <script type="text/javascript">
        //Red color scheme for iCheck
        $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
            checkboxClass: 'icheckbox_minimal-red',
            radioClass: 'iradio_minimal-red'
        });

        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });

        $(".add-bed").click(function () {
            $('.box-ex-bed').slideToggle("fast");
        })

        
    </script>
@stop