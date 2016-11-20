@extends('layouts.master')

@section('css')
    <style type="text/css">
        .sm-lb{font-weight: normal;font-size: 16px;}
        .bg-lb{font-size: 18px; text-transform: uppercase;}
        .content-box .form-group{margin-bottom: 40px;}
        .input-group[class*=col-] {float: left;!important; padding: 0px 15px!important;}
        .r2{width: 50px;}
        .type-price{margin-left: 15px;}
        .policy{margin-left: 30px;}
        .opt-policy{float: left; margin-left: 5px;}
        .opt-policy label{padding-top: 7px; font-weight: normal;}
        .text-muted{height: auto;}
        .fa-plus-square{color: #0C59CF;}
        .fa-minus-square{color: pink;}
        .add-period{color: red; font-size: 15px;}
        .conv{margin-left: 20px; font-weight: normal; cursor: pointer;}
        .extra{font-weight: normal; margin-top: 7px;}
        .lb-extra{margin-left: 38px;}
        .info-add-child .col-sm-2{width: auto;}
        .extra-box{margin-left: 15px;}
        .box-success{background-color: #F5F5F5;}
    </style>
@stop

@section('title')
	Add new rate plan
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
    	<div class="box box-info">
            {!! Form::open(['name' => 'rate-plan', 'method' => 'POST', 'id' => 'rate-plan']) !!}
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
                <h3 class="box-title">Tạo đơn giá</h3>
            </div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="bg-lb">Tên hệ thống giá</label>

                    </div>
                        <div class="box-body content-box">
                            <div class="form-group">
                                <div class="col-sm-6">
                                    {!! Form::text('rap_title', '', ['class' => 'form-control']) !!}
                                    <div><i class="fa fa-fw fa-info-circle" style="color: blue;"></i><span><i style="font-size: 12px;">Tên này dùng để Khách sạn quản lý giá, không hiển thị trên website</i></span></div>
                                </div>
                            </div>



                        </div>
                    <!-- /.box-body-1 -->

                    <div class="form-group">
                        <label class="bg-lb">Loại giá</label>

                    </div>
                        <div class="box-body content-box">
                            <div class="form-group">
                                <label class="type-price price">
                                    {!! Form::radio('rap_type_price', '1', true, ['class' => 'minimal-red']) !!}
                                    Khách lẻ (OTA)</label>
                                <br/>
                                @if($hotel_type['ota'] == 0)
                                <label class="type-price price">
                                    {!! Form::radio('rap_type_price', '0', false, ['class' => 'minimal-red']) !!}
                                    Đại lý (TA)</label>
                                @endif
                            </div>

                        </div>
                    <!-- /.box-body-2 -->

                    <div class="form-group">
                        <label class="bg-lb">Áp dụng loại phòng</label>
                    </div>
                        <div class="box-body content-box">
                            <div class="form-group">
                                @foreach($data as $key => $room)
                                    <label class="type-price">
                                        <input type="checkbox" class="flat-red col-sm-3 control-label" name="rap_room_apply_id[]" value="{{$room['room_id']}}">

                                        {{ $room['room_name'] }}
                                    </label>
                                    <br/>
                                @endforeach
                            </div>
                        </div>
                    <!-- /.box-body-3 -->

                    <div class="form-group">
                        <label class="bg-lb">Chính sách hủy</label>
                    </div>
                        <div class="box-body content-box">
                            <div class="form-group">
                                <label class="type-price sl-plc">
                                    <input type="radio" name="type_policy" class="minimal-red" checked value="1">
                                    Không hoàn hủy</label>
                                <br/>
                                <label class="type-price sl-plc">
                                    <input type="radio" name="type_policy" class="minimal-red" value="2">
                                    Tùy chỉnh</label>
                            </div>
                            <div class="form-group policy" style="display: none;">
                                <div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                    <div>
                                        <div class="opt-policy">
                                            <label>Trong vòng  </label>
                                        </div>
                                        <div class="opt-policy">
                                            <select class="form-control col-sm-4" name="rap_policy_day[]" data-first-day="true" id="rap_policy_day">
                                                @for($i = 1;$i < 31;$i++)
                                                    <option value="{{ $i }}">{{ $i }} ngày</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="opt-policy">
                                            <label>trước khi đến, tính phí: </label>
                                        </div>
                                        <div class="opt-policy">
                                            {!! Form::select('rap_policy_fee[]', $option, 10, ['class' => 'form-control col-sm-4']) !!}
                                        </div>
                                    </div>
                                    <div style="clear: both; margin-bottom: 20px;"></div>
                                    <div>
                                        <div class="opt-policy">
                                            <label>Trước khi nhận phòng</label>
                                        </div>
                                        <div class="opt-policy">
                                            <select class="form-control col-sm-4 rap_policy_day_hidden" name="rap_policy_day[]" disabled>
                                                @for($i = 1;$i < 31;$i++)
                                                    <option value="{{ $i }}">{{ $i }} ngày</option>
                                                @endfor
                                            </select>
                                            <input name="rap_policy_day[]" class="rap_policy_day_hidden" value="1" type="hidden">
                                        </div>
                                        <div class="opt-policy">
                                            <label style="margin-left: 22px;">tính phí: </label>
                                        </div>
                                        <div class="opt-policy">
                                            <select class="form-control col-sm-4" name="rap_policy_fee[]">
                                                <option value="0">Miễn phí</option>
                                                <option value="11">1 đêm đầu tiên</option>
                                                <option value="12">2 đêm đầu tiên</option>
                                                @for($i = 1;$i <= 10;$i++)
                                                    <option value="{{ $i }}">{{ $i * 10 }}% giá trị đơn phòng</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="opt-policy btn-plc">
                                            <i class="fa fa-fw fa-plus-square add-plc" style="font-size: 33px; margin-left: 10px; cursor: pointer;"></i>
                                        </div>
                                    </div>
                                    <div style="clear: both; margin-bottom: 20px;" class="result-add"></div>
                                    <div class="box box-default collapsed-box policy-gr" style="width:75%;display: none;">
                                        <div class="box-header with-border">
                                          <h3 class="box-title" style="font-size: 14px;">Thêm chính sách hủy dành cho khách đoàn</h3>

                                          <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse" name="cancellation_policy_group"><i class="fa fa-plus"></i>
                                            </button>
                                          </div>
                                          <!-- /.box-tools -->
                                        </div>
                                        <!-- /.box-header -->
                                        <div class="box-body">
                                            <div class="add-policy">
                                                <div class="opt-policy">
                                                    <label>Áp dụng khi đơn phòng có</label>
                                                </div>
                                                <div class="opt-policy">
                                                    <input type="text" class="form-control" id="policy_group_room" name="policy_group_room" placeholder="">
                                                </div>
                                                <div class="opt-policy">
                                                    <label>phòng trở lên. </label>
                                                </div>
                                                <div style="clear: both; margin-bottom: 20px;"></div>
                                                <div class="opt-policy">
                                                    <label>Trong vòng </label>
                                                </div>
                                                <div class="opt-policy">
                                                    <select class="form-control col-sm-4 policy_group_day" name="policy_group_day[]">
                                                        @for($i = 1;$i < 31;$i++)
                                                            <option value="{{ $i }}">{{ $i }} ngày</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="opt-policy">
                                                    <label>trước khi đến, tính phí: </label>
                                                </div>
                                                <div class="opt-policy">
                                                    {!! Form::select('policy_group_fee[]', $option, 10, ['class' => 'form-control col-sm-4']) !!}
                                                </div>
                                                <div style="clear: both; margin-bottom: 20px;"></div>
                                                <div class="opt-policy">
                                                    <label>Trước khi nhận phòng</label>
                                                </div>
                                                <div class="opt-policy">
                                                    <select class="form-control col-sm-4 policy_group_day_hidden" name="policy_group_day[]" disabled="">
                                                        @for($i = 1;$i < 31;$i++)
                                                            <option value="{{ $i }}">{{ $i }} ngày</option>
                                                        @endfor
                                                    </select>
                                                    <input name="policy_group_day[]" class="policy_group_day_hidden" value="1" type="hidden">
                                                </div>
                                                <div class="opt-policy">
                                                    <label style="margin-left: 22px;">tính phí: </label>
                                                </div>
                                                <div class="opt-policy">
                                                    <select class="form-control col-sm-4" name="policy_group_fee[]">
                                                        <option value="0">Miễn phí</option>
                                                        <option value="11">1 đêm đầu tiên</option>
                                                        <option value="12">2 đêm đầu tiên</option>
                                                        @for($i = 1;$i <= 10;$i++)
                                                            <option value="{{ $i }}">{{ $i * 10 }}% giá trị đơn phòng</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="opt-policy btn-add-extra">
                                                    <i class="fa fa-fw fa-plus-square add-plc-extra" style="font-size: 33px; margin-left: 10px; cursor: pointer;"></i>
                                                </div>
                                            </div>
                                            <div style="clear: both;margin-bottom:10px;" class="result-add-extra"></div>
                                        </div>
                                        <!-- /.box-body -->
                                      </div>
                                    <div style="clear: both;"></div>

                                </div>

                                <label style="margin-left: 5px; font-weight: normal; color: #0C59CF; cursor: pointer;"  class="result-add-period">
                                    Thêm giai đoạn cao điểm <i class="fa fa-fw fa-plus-square-o"></i>
                                </label>
                                (Giai đoạn Lễ, Tết, mùa hè,...)

                            </div>
                        </div>
                    <!-- /.box-body-3 -->

                     <div class="form-group">
                        <label class="bg-lb">Phụ thu</label>
                    </div>
                        <div class="box-body content-box">
                            <div class="form-group">
                                <label class="extra-box">
                                    <input type="checkbox" class="flat-red col-sm-3 control-label" name="add_extra_bed" id="add_extra_bed" value="1">

                                    Thêm giường phụ
                                </label>
                                <div class="form-group info-add-bed" style="display: none;">
                                    <label class="col-sm-2 control-label extra lb-extra">Phí thêm giường</label>

                                    <div class="col-sm-3">
                                        {!! Form::text('bed_extra_price', null, ['class' => 'form-control input-price']) !!}
                                    </div>
                                    <label class="col-sm-3 control-label extra">VNĐ / đêm</label>
                                    <div style="clear: both;"></div>
                                    <div class="form-group" style="margin-top: 10px;">
                                    <span style="margin-left: 53px; margin-bottom: 10px;">Không áp dụng cho các phòng : </span><br/>
                                        @foreach($data as $key => $room)
                                            <label class="type-price col-sm-4" style="cursor: pointer;">
                                                <input style="cursor: pointer;" type="checkbox" class="col-sm-2 control-label" name="rap_room_apply_bed[]" value="{{$room['room_id']}}">

                                                {{ $room['room_name'] }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div style="clear: both;"></div>
                                <label class="extra-box">
                                    <input type="checkbox" class="flat-red col-sm-3 control-label" name="add_extra_adult" id="add_extra_adult" value="1">

                                    Thêm người lớn
                                </label>
                                <div class="form-group info-add-adult" style="display: none;">
                                    <label class="col-sm-2 control-label extra lb-extra">Số lượng tối đa</label>

                                    <div class="col-sm-3">
                                        {!! Form::text('number_adult', null, ['class' => 'form-control']) !!}
                                    </div>
                                    <label class="col-sm-3 control-label extra">người</label>
                                    <div style="clear: both;margin-bottom:10px;"></div>
                                    <label class="col-sm-2 control-label extra lb-extra">Phí thêm người</label>

                                    <div class="col-sm-3">
                                        {!! Form::text('price_adult', null, ['class' => 'form-control input-price']) !!}
                                    </div>
                                    <label class="col-sm-3 control-label extra">VNĐ / người / đêm</label>
                                </div>
                                <br/>
                                <label class="extra-box">
                                    <input type="checkbox" class="flat-red col-sm-3 control-label" name="add_extra_child" id="add_extra_child" value="1">

                                    Thêm trẻ em
                                </label>
                                <div class="form-group info-add-child" style="display: none;">
                                    <label class="col-sm-2 control-label extra lb-extra">Số lượng tối đa</label>

                                    <div class="col-sm-3">
                                        {!! Form::text('number_child', null, ['class' => 'form-control']) !!}
                                    </div>

                                    <div class="add-childs">
                                        <div style="clear: both;margin-bottom:10px;"></div>
                                        <label class="col-sm-1 control-label extra lb-extra">Trẻ từ</label>
                                        <div class="col-sm-1">
                                            {!! Form::text('min_child[1]', null, ['class' => 'form-control']) !!}
                                        </div>
                                        <label class="col-sm-1 control-label extra">đến</label>
                                        <div class="col-sm-1">
                                            {!! Form::text('max_child[1]', null, ['class' => 'form-control']) !!}
                                        </div>
                                        <label class="col-sm-2 control-label extra">Phụ thu</label>
                                        <div class="col-sm-3">
                                            {!! Form::text('extra_child[1]', null, ['class' => 'form-control input-price']) !!}
                                        </div>
                                        <label class="col-sm-2 control-label extra">VNĐ / đêm</label>
                                    </div>

                                    <div class="btn-add-extra">
                                        <i class="fa fa-fw fa-plus-square add-extra-child" style="font-size: 33px; margin-left: 10px; cursor: pointer;"></i>
                                    </div>
                                    <div style="clear: both;margin-bottom:10px;" class="append"></div>
                                    <label class="col-sm-1 control-label extra lb-extra">Trẻ từ</label>
                                    <div class="col-sm-1">
                                        {!! Form::text('child_adult', null, ['class' => 'form-control']) !!}
                                    </div>
                                    <label class="col-sm-3 control-label extra">trở lên được tính như người lớn</label>
                                </div>
                            </div>
                        </div>
                    <!-- /.box-body-4 -->

                    <div class="form-group">
                        <label class="bg-lb">Dịch vụ đi kèm</label>
                    </div>
                        <div class="box-body content-box">
                            <div class="form-group">
                                <label>
                                    Bữa ăn
                                </label>
                                <label class="conv">
                                    <input type="checkbox" class="flat-red col-sm-3 control-label conv-1 convenien" name="conv[]" value="1">

                                    Bữa sáng
                                </label>
                                <label class="conv">
                                    <input type="checkbox" class="flat-red col-sm-3 control-label conv-2 convenien" name="conv[]" value="2">

                                    Bữa trưa
                                </label>
                                <label class="conv">
                                    <input type="checkbox" class="flat-red col-sm-3 control-label conv-3 convenien" name="conv[]" value="3">

                                    Bữa tối
                                </label>
                                <label class="conv">
                                    <input type="checkbox" class="flat-red col-sm-3 control-label conv-4 convenien" name="conv[]" value="4">

                                    Tất cả
                                </label>
                            </div>
                        </div>
                    <!-- /.box-body-4 -->

                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-block btn-primary col-sm-3">Submit</button>
                    </div>
                    <div class="col-sm-2" style="padding-top: 5px; margin-left: 30px;">
                        <a href="">Reset</a>
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

        $('input#add_extra_bed').on('ifChanged', function(event){
            var checked = !$(this).parent('[class*="icheckbox"]').hasClass("checked");
            if(checked) {
                $(".info-add-bed").show();
            } else {
                $(".info-add-bed").hide();
            }

        });

        $('input#add_extra_adult').on('ifChanged', function(event){
            var checked = !$(this).parent('[class*="icheckbox"]').hasClass("checked");
            if(checked) {
                $(".info-add-adult").show();
            } else {
                $(".info-add-adult").hide();
            }

        });

        $('input#add_extra_child').on('ifChanged', function(event){
            var checked = !$(this).parent('[class*="icheckbox"]').hasClass("checked");
            if(checked) {
                $(".info-add-child").show();
            } else {
                $(".info-add-child").hide();
            }

        });


        $('#rap_policy_day').change(function(){
            $('input.rap_policy_day_hidden').val($(this).val());
            var valuaSet = $(this).val();
            $("select.rap_policy_day_hidden option").removeAttr('selected');
               // console.log($("select.rap_policy_day_hidden").children());
            $("select.rap_policy_day_hidden").find('option[value='+valuaSet+']').prop('selected', true);

        });

        $('.policy_group_day').change(function(){
            $('input.policy_group_day_hidden').val($(this).val());
            var valuaSet = $(this).val();
            $("select.policy_group_day_hidden option").removeAttr('selected');
               // console.log($("select.rap_policy_day_hidden").children());
            $("select.policy_group_day_hidden").find('option[value='+valuaSet+']').prop('selected', true);

        });


        $(".box-body").on('change', ".top_period_day", function (event) {
            i = $(this).data('box');
            $('input.top_period_day_hidden_' + i).val($(this).val());
            var valuaSet = $(this).val();
            $("select.top_period_day_hidden_" + i + " option").removeAttr('selected');
               // console.log($("select.top_period_day_hidden").children());
            $("select.top_period_day_hidden_" + i).find('option[value='+valuaSet+']').prop('selected', true);
        });

        $('.conv-4').on('ifChanged', function(event) {
            var checked = !$(this).parent('[class*="icheckbox"]').hasClass("checked");
            if(checked){
                $('input.convenien').iCheck('check');
            } else {
                $('input.convenien').iCheck('uncheck');
            }
        });

        $('.add-extra-child').click(function(){
            rcmAddChild();
        });

        $(".add-plc").click(function () {
            rcmAddPolicy();
        })

        $(".add-plc-extra").click(function () {
            rcmAddPolicyExtra();
        })

        $(".remove-plc").click(function () {
            rcmRemovePolicy();
        })

        $(".result-add-period").click(function () {
            period = $('.box-success').length + 1;
            rcmAddTopPeriod(period);
            // $('.box-success').each(function(index, ele){
            //     index += 1;
            //     $(this).find('h3.add-period').text("Giai đoạn cao điểm " + index);
            //     $(this).find('select.top_period_day').attr('data-box',index);
            //     $(this).find('select.top_period_fee').attr('name','top_period_fee_' + index + '[]');
            // });
            //Date range picker
            $('.reservation').daterangepicker({
                format: 'DD/MM/YYYY'
            });
        });

        $(".sl-plc,.iCheck-helper,.price").click(function () {
            var selectedPrice = $("input[type='radio'][name='rap_type_price']:checked").val();
            var selected = $("input[type='radio'][name='type_policy']:checked").val();
            if(selected == 2){
                $('.policy').show();
            }
            if(selected == 1){
                $('.policy').hide();
            }
            if(selectedPrice == 1) {
                $('.policy-gr').hide();
            }
            if(selectedPrice == 0) {
                $('.policy-gr').show();
            }

            if(selectedPrice == 0) {
                $('.policy-gr-period').show();
            } else {
                $('.policy-gr-period').hide();
            }

            // var selectedExtra = $("input[type='checkbox'][name='extra_money[]']:checked").val();
            // if(selectedExtra == 'on'){
            //     $('.info-add-bed').show();
            // } else {
            //     $('.info-add-bed').hide();
            // }

        });

        $(".box-body").on('click', "i.add-period-extra", function (event) {
            event.stopPropagation();
            exPeriod = $(this).data('btn');
            rcmAddTopPeriodExtra(exPeriod);
        });

        var rcmAddPolicy = function () {
            var str_row = '<div style="display: none;" class="added">';
            str_row += '<div style="clear: both; margin-bottom:10px;" class="added"></div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label>Trước khi nhận phòng</label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<select class="form-control col-sm-4" name="rap_policy_day[]">';
            for (var i = 1; i < 31; i++) {
                str_row += '<option value="' + i + '">' + i + ' ngày</option>';
            };
            str_row += '</select>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label style="margin-left: 22px;">tính phí: </label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<select class="form-control col-sm-4" name="rap_policy_fee[]">';
            str_row += '<option value="0">Miễn phí</option>';
            str_row += '<option value="11">1 đêm đầu tiên</option>';
            str_row += '<option value="12">2 đêm đầu tiên</option>';
            for (var i = 1; i <= 10; i++) {
                str_row += '<option value="' + i + '">' + i * 10 + '% giá trị đơn phòng</option>';
            };
            str_row += '</select>';
            str_row += '</div>';
            str_row += '<i onclick="rcmRemovePolicy(this);" class="fa fa-fw fa-minus-square remove-plc" style="font-size: 33px; margin-left: 15px; cursor: pointer;"></i>';
            str_row += '<div style="clear: both; margin-bottom:10px;" class="added"></div>';
            str_row += '</div>';

            $.when($('.result-add').before(str_row)).then(function () {
                $('div.added').show(300);
            });
        }

        var rcmRemovePolicy = function (object) {
            $(object).parents('div.added').hide(300, function () {
                $(object).parents('div.added').remove();
            });
        }

        var rcmAddPolicyExtra = function () {
            var str_row = '<div style="display: none;" class="added">';
            str_row += '<div style="clear: both; margin-bottom:10px;" class="added"></div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label>Trước khi nhận phòng</label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<select class="form-control col-sm-4" name="policy_group_day[]">';
            for (var i = 1; i < 31; i++) {
                str_row += '<option value="' + i + '">' + i + ' ngày</option>';
            };
            str_row += '</select>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label style="margin-left: 22px;">tính phí: </label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<select class="form-control col-sm-4" name="policy_group_fee[]">';
            str_row += '<option value="0">Miễn phí</option>';
            str_row += '<option value="11">1 đêm đầu tiên</option>';
            str_row += '<option value="12">2 đêm đầu tiên</option>';
            for (var i = 1; i <= 10; i++) {
                str_row += '<option value="' + i + '">' + i * 10 + '% giá trị đơn phòng</option>';
            };
            str_row += '</select>';
            str_row += '</div>';
            str_row += '<i onclick="rcmRemovePolicy(this);" class="fa fa-fw fa-minus-square remove-plc" style="font-size: 33px; margin-left: 15px; cursor: pointer;"></i>';
            str_row += '<div style="clear: both; margin-bottom:10px;" class="added"></div>';
            str_row += '</div>';

            $.when($('.result-add-extra').before(str_row)).then(function () {
                $('div.added').show(300);
            });
        }

        $('body').on('click', '.remove-period', function(){
            $(this).parents('div.box-success').remove();
            $('.box-success').each(function(index, ele){
                index += 1;
                $(this).find('h3.add-period').text("Giai đoạn cao điểm " + index);
                $(this).find('select.top_period_day').attr({'data-box':index,
                                                            'name':'top_period_day_' + index + '[]'});
                $(this).find('select.top_period_fee').attr('name','top_period_fee_' + index + '[]');
                $(this).find('select.period_hidden').attr({'class':'form-control col-sm-4 period_hidden top_period_day_hidden_' + index,
                                                           'name':'top_period_day_' + index + '[]'});
                $(this).find('input.period_hidden').attr({'class':'period_hidden top_period_day_hidden_' + index,
                                                          'name':'top_period_day_' + index + '[]'});
                $(this).find('i.add-period-extra').attr('data-btn',index);
                $(this).find('div.result').attr('class','result-add-period-extra-' + index);
            });
        });
        var rcmAddTopPeriod = function (period) {
            var str_row = '<div class="box box-success box-solid added opt-policy-' + period + '" style="margin-top: 10px;padding-left: 10px;">';
            str_row += '<div>';
            str_row += '<div class="box-header with-border">';
            str_row += '<h3 class="box-title add-period">Giai đoạn cao điểm ' + period + '</h3>';
            str_row += '<div class="box-tools pull-right remove-period">';
            str_row += '<button type="button" ><i class="fa fa-times"></i></button>';
            str_row += '</div>';
            str_row += '</div>';
            str_row += '<div class="form-group">';
            str_row += '<label>Khoảng thời gian:</label>';
            str_row += '<div class="input-group">';
            str_row += '<div class="input-group-addon">';
            str_row += '<i class="fa fa-calendar"></i>';
            str_row += '</div>';
            str_row += '<input type="text" name="top_period_time[]" class="form-control pull-right col-sm-4 reservation" style="width:70%;float: left!important;">';
            str_row += '</div>';
            str_row += '<label style="width:20%; cursor: pointer;">';
            str_row += '<input type="checkbox" class="flat-red col-sm-1 control-label check_period" data-check="' + period + '" name="check_period_' + period + '" value="1">Không hoàn hủy';
            str_row += '</label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label>Trong vòng </label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<select class="form-control col-sm-4 top_period_day" data-box="' + period + '" name="top_period_day_' + period +'[]">';
            for (var i = 1; i < 31; i++) {
                str_row += '<option value="' + i + '">' + i + ' ngày</option>';
            };
            str_row += '</select>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label>trước khi đến, tính phí: </label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<select class="form-control col-sm-4 top_period_fee" name="top_period_fee_' + period + '[]">';
            str_row += '<option value="0">Miễn phí</option>';
            str_row += '<option value="11">1 đêm đầu tiên</option>';
            str_row += '<option value="12">2 đêm đầu tiên</option>';
            for (var i = 1; i <= 10; i++) {
                if(i == 10){
                    str_row += '<option value="' + i + '" selected>' + i * 10 + '% giá trị đơn phòng</option>';
                } else {
                    str_row += '<option value="' + i + '" >' + i * 10 + '% giá trị đơn phòng</option>';
                }
            };
            str_row += '</select>';
            str_row += '</div>';
            str_row += '<div style="clear: both; margin-bottom: 20px;"></div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label>Trước khi nhận phòng</label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<select class="form-control col-sm-4 period_hidden top_period_day_hidden_' + period +'" name="top_period_day_' + period +'[]" disabled>';
            for (var i = 1; i < 31; i++) {
                str_row += '<option value="' + i + '">' + i + ' ngày</option>';
            };
            str_row += '</select>';
            str_row += '<input name="top_period_day_' + period +'[]" class="period_hidden top_period_day_hidden_' + period +'" value="1" type="hidden">';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label style="margin-left: 22px;">tính phí: </label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<select class="form-control col-sm-4 top_period_fee" name="top_period_fee_' + period + '[]">';
            str_row += '<option value="0">Miễn phí</option>';
            str_row += '<option value="11">1 đêm đầu tiên</option>';
            str_row += '<option value="12">2 đêm đầu tiên</option>';
            for (var i = 1; i <= 10; i++) {
                str_row += '<option value="' + i + '">' + i * 10 + '% giá trị đơn phòng</option>';
            };
            str_row += '</select>';
            str_row += '</div>';
            str_row += '<div class="opt-policy btn-add-period-extra">';
            str_row += '<i class="fa fa-fw fa-plus-square add-period-extra" data-btn="' + period + '" style="font-size: 33px; margin-left: 10px; cursor: pointer;"></i>';
            str_row += '</div>';
            str_row += '<div style="clear: both; margin-bottom: 20px;" class="result result-add-period-extra-' + period + '"></div>';    
            str_row += '</div>';


            str_row += '<div class="box box-default collapsed-box policy-gr-period" style="width:75%;display: none;">';
            str_row += '<div class="box-header with-border">';
            str_row += '<h3 class="box-title" style="font-size: 14px;">Thêm chính sách hủy dành cho khách đoàn</h3>';
            str_row += '<div class="box-tools pull-right">';
            str_row += '<button type="button" class="btn btn-box-tool" data-widget="collapse" name="cancellation_policy_group"><i class="fa fa-plus group-period"></i>';
            str_row += '</button>';
            str_row += '</div>';
            str_row += '</div>';
            str_row += '<div class="box-body" style="display: none;">';
            str_row += '<div class="add-policy">';
            str_row += '<div class="opt-policy">';
            str_row += '<label>Áp dụng khi đơn phòng có</label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<input type="text" class="form-control" id="group_period_room" name="group_period_room_' + period + '" placeholder="">';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label>phòng trở lên. </label>';
            str_row += '</div>';
            str_row += '<div style="clear: both; margin-bottom: 20px;"></div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label>Trong vòng </label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<select class="form-control col-sm-4 group_period_day" data-box="' + period + '" name="group_period_day_' + period + '[]">';
            for(var i = 1; i < 31; i++){
                str_row += '<option value="' + i + '">' + i + ' ngày</option>';
            };
            str_row += '</select>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label>trước khi đến, tính phí: </label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label style="margin-left: 22px;">tính phí: </label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<select class="form-control col-sm-4" name="group_period_fee_' + period + '[]">';
            str_row += '<option value="0">Miễn phí</option>';
            str_row += '<option value="11">1 đêm đầu tiên</option>';
            str_row += '<option value="12">2 đêm đầu tiên</option>';
            for(var i = 1; i <= 10; i++) {
                if(i == 10){
                    str_row += '<option value="' + i + '" selected>' + i * 10 + '% giá trị đơn phòng</option>';
                } else {
                    str_row += '<option value="' + i + '" >' + i * 10 + '% giá trị đơn phòng</option>';
                }
            };
            str_row += '</select>';
            str_row += '</div>';
            str_row += '<div style="clear: both; margin-bottom: 20px;"></div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label>Trước khi nhận phòng</label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<select class="form-control col-sm-4 group_period_day_hidden_' + period + '" name="group_period_day_' + period + '[]" disabled="">';
            for(var i = 1; i < 31; i++) {
                str_row += '<option value="' + i + '">' + i + ' ngày</option>';
            };
            str_row += '</select>';
            str_row += '<input name="group_period_day_' + period + '[]" class="group_period_day_hidden_' + period + '" value="1" type="hidden">';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label style="margin-left: 22px;">tính phí: </label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<select class="form-control col-sm-4" name="group_period_fee_' + period + '[]">';
            str_row += '<option value="0">Miễn phí</option>';
            str_row += '<option value="11">1 đêm đầu tiên</option>';
            str_row += '<option value="12">2 đêm đầu tiên</option>';
            for(var i = 1; i <= 10; i++) {
                str_row += '<option value="' + i + '">' + i * 10 + '% giá trị đơn phòng</option>';
            };
            str_row += '</select>';
            str_row += '</div>';
            str_row += '<div class="opt-policy btn-add-extra">';
            str_row += '<i class="fa fa-fw fa-plus-square add-group-period" data-btn="' + period + '" style="font-size: 33px; margin-left: 10px; cursor: pointer;"></i>';
            str_row += '</div>';
            str_row += '<div style="clear: both;margin-bottom:10px;" class="result-add-group_' + period + '"></div>';
            str_row += '</div>';
            str_row += '</div>';
            str_row += '<div style="clear: both;"></div>';
            str_row += '</div>';

            $.when($('.result-add-period').before(str_row)).then(function () {
                $('div.added').show(300);
            });
        }

        $('body').on('click', '.result-add-period', function(){
            var type_price = $("input[type='radio'][name='rap_type_price']:checked").val();

            if(type_price == 0) {
                $('.policy-gr-period').show();
            } else {
                $('.policy-gr-period').hide();
            }

        });

        $(".box-body").on('click', "i.add-group-period", function (event) {
            event.stopPropagation();
            exPeriod = $(this).data('btn');
            rcmAddGroupPeriod(exPeriod);
        });

        $(".box-body").on('change', ".group_period_day", function (event) {
            i = $(this).data('box');
            $('input.group_period_day_hidden_' + i).val($(this).val());
            var valuaSet = $(this).val();
            $("select.group_period_day_hidden_" + i + " option").removeAttr('selected');
               // console.log($("select.top_period_day_hidden").children());
            $("select.group_period_day_hidden_" + i).find('option[value='+valuaSet+']').prop('selected', true);
        });

        $(".box-body").on('change', ".check_period", function (event) {
            event.stopPropagation();
            var valueCheck = $(this).is(':checked'); ;
            var period = $(this).data('check');
            if(valueCheck){
                $('.opt-policy-' + period).find('select').attr('disabled', true);
                $('.opt-policy-' + period).find('i.add-period-extra').hide();
            } else {
                $('.opt-policy-' + period).find('select').removeAttr('disabled');
                $('.opt-policy-' + period).find('i.add-period-extra').show();
                $('.period_hidden').attr('disabled', true);
            }
        });

        var rcmAddGroupPeriod = function (period) {
            var str_row = '<div style="display: none;" class="added-group">';
            str_row += '<div style="clear: both; margin-bottom:10px;" class="added-group"></div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label>Trước khi nhận phòng</label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<select class="form-control col-sm-4" name="group_period_day_' + period + '[]">';
            for (var i = 1; i < 31; i++) {
                str_row += '<option value="' + i + '">' + i + ' ngày</option>';
            };
            str_row += '</select>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label style="margin-left: 22px;">tính phí: </label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<select class="form-control col-sm-4" name="group_period_fee_' + period + '[]">';
            str_row += '<option value="0">Miễn phí</option>';
            str_row += '<option value="11">1 đêm đầu tiên</option>';
            str_row += '<option value="12">2 đêm đầu tiên</option>';
            for (var i = 1; i <= 10; i++) {
                str_row += '<option value="' + i + '">' + i * 10 + '% giá trị đơn phòng</option>';
            };
            str_row += '</select>';
            str_row += '</div>';
            str_row += '<i onclick="rcmRemovePolicy(this);" class="fa fa-fw fa-minus-square remove-plc" style="font-size: 33px; margin-left: 15px; cursor: pointer;"></i>';
            str_row += '<div style="clear: both; margin-bottom:10px;" class="added-group"></div>';
            str_row += '</div>';

            $.when($('.result-add-group_' + period).before(str_row)).then(function () {
                $('div.added-group').show(300);
            });
        }

        var rcmAddTopPeriodExtra = function (period) {
            var str_row = '<div style="display: none;" class="added-ex">';
            str_row += '<div style="clear: both; margin-bottom:10px;" class="added"></div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label>Trước khi nhận phòng</label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<select class="form-control col-sm-4" name="top_period_day_' + period +'[]">';
            for (var i = 1; i < 31; i++) {
                str_row += '<option value="' + i + '">' + i + ' ngày</option>';
            };
            str_row += '</select>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<label style="margin-left: 22px;">tính phí: </label>';
            str_row += '</div>';
            str_row += '<div class="opt-policy">';
            str_row += '<select class="form-control col-sm-4" name="top_period_fee_' + period +'[]">';
            str_row += '<option value="0">Miễn phí</option>';
            str_row += '<option value="11">1 đêm đầu tiên</option>';
            str_row += '<option value="12">2 đêm đầu tiên</option>';
            for (var i = 1; i <= 10; i++) {
                str_row += '<option value="' + i + '">' + i * 10 + '% giá trị đơn phòng</option>';
            };
            str_row += '</select>';
            str_row += '</div>';
            str_row += '<i onclick="rcmRemovePeriod(this);" class="fa fa-fw fa-minus-square" style="font-size: 33px; margin-left: 15px; cursor: pointer;"></i>';
            str_row += '<div style="clear: both; margin-bottom:10px;" class="added"></div>';
            str_row += '</div>';

            $.when($('.result-add-period-extra-' + period).before(str_row)).then(function () {
                $('div.added-ex').show(300);
            });
        }

        var rcmRemovePeriod = function (object) {
            $(object).parents('div.added-ex').hide(300, function () {
                $(object).parents('div.added-ex').remove();
            });
        }

        var rcmAddChild = function () {
            var str_row = '<div class="add-childs">';
            str_row += '<div style="clear: both;margin-bottom:10px;"></div>';
            str_row += '<label class="col-sm-1 control-label extra lb-extra">Trẻ từ</label>';
            str_row += '<div class="col-sm-1">';
            str_row += '<input type="text" class="form-control" name="min_child[]" placeholder="">';
            str_row += '</div>';
            str_row += '<label class="col-sm-1 control-label extra">đến</label>';
            str_row += '<div class="col-sm-1">';
            str_row += '<input type="text" class="form-control" name="max_child[]" placeholder="">';
            str_row += '</div>';
            str_row += '<label class="col-sm-2 control-label extra">Phụ thu</label>';
            str_row += '<div class="col-sm-3">';
            str_row += '<input type="text" class="form-control input-price" name="extra_child[]" placeholder="">';
            str_row += '</div>';
            str_row += '<label class="col-sm-2 control-label extra">VNĐ / đêm</label>';
            str_row += '<i onclick="rcmRemoveBoxChild(this);" class="fa fa-fw fa-minus-square remove-period" style="font-size: 33px; margin-left: 15px; cursor: pointer;"></i>';
            str_row += '</div>';


            $.when($('.append').before(str_row)).then(function () {
                $('div.add-childs').show(300);
            });

            $('input.input-price, .input-price input').on('keyup', function(event){
                // skip for arrow keys
                if(event.which >= 37 && event.which <= 40){
                    event.preventDefault();
                }
                var $this = $(this);
                var num = $this.val().replace(/,/gi, "").split("").reverse().join("");

                var num2 = RemoveRougeChar(num.replace(/(.{3})/g,"$1,").split("").reverse().join(""));

                // the following line has been simplified. Revision history contains original.
                $this.val(num2);
            });

            function RemoveRougeChar(convertString) {
                if (convertString.substring(0,1) == ",") {
                    return convertString.substring(1, convertString.length)
                }

                return convertString;
            }
        }

        var rcmRemoveBoxChild = function (object) {
            $(object).parents('div.add-childs').hide(300, function () {
                $(object).parents('div.add-childs').remove();
            });
        }

    </script>
    <script src="{{ asset('mytour/js/mytour.input_currency.js') }}"></script>
@stop
