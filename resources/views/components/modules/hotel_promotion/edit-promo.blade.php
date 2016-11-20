@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/datetimepicker/bootstrap-datetimepicker.min.css') }}">
    <style type="text/css">
        table.vertical-middle,
        table.vertical-middle td,
        table.vertical-middle th {
            vertical-align: middle !important;
        }
        .bg-gray{background-color: #F7F6F6 !important}
        .bg-light-pink{background-color: #FFECD7}
        .checkbox-week .checkbox{padding-right: 15px}
        .checkbox-week .pull-left:last-child .checkbox{padding-right: 0}
        span.btn-add-input{padding-left: 15px;}
        .btn-add-input .btn-flat{padding: 1px 4px;border-radius: 5px !important}
        .padding-10{padding: 10px;}
        .day-not-apply .input-group{padding-bottom: 10px}
    </style>
@stop

@section('title')
    Chỉnh sửa thông tin khuyến mãi
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">

            {!! Form::model($infoPromo, ['method' => 'PATCH', 'route' => ['update-hotel-promo', $infoPromo->proh_id]]) !!}
                @include('layouts.includes.show-error-validate')

                <div class="box-body">
                    <div class="form-group">
                        <label for="">Tiêu đề Khuyến mãi <i class="text-red">*</i></label>
                        {!! Form::text($prefix . '_proh_title', $infoPromo->proh_title, ['class' => 'form-control', 'id' => 'proh_title']) !!}
                        {!! Form::hidden('hotel_id', $infoPromo->proh_hotel) !!}
                        {!! Form::hidden('proh_type', $infoPromo->proh_type) !!}
                    </div>
                </div>
                <div class="box-header with-border">
                    <h3 class="box-title">Thời gian ở</h3>
                    <i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Chọn ngày nhận phòng được áp dụng khuyến mãi</i>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        {!! Form::label($prefix . '_date_range_checkin', 'Khoảng thời gian nhận phòng') !!}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            {!! Form::text($prefix . '_date_range_checkin', date("d/m/Y", $infoPromo->proh_time_start) . " - " . date("d/m/Y", $infoPromo->proh_time_finish), ['class' => 'form-control pull-right date-range']) !!}
                        </div>
                    </div>

                    <div class="form-group col-sm-6">
                        <div class="bg-light-pink padding-10 day-not-apply">
                            {!! Form::label('proh_day_deny', 'Ngày không áp dụng') !!}
                            <?
                                $proh_day_deny = $infoPromo->proh_day_deny;
                                $i = 1;
                            ?>
                            @if (!$proh_day_deny->isEmpty())
                                @foreach($proh_day_deny as $timeInt)
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input class="form-control date-picker" name="proh_day_deny[]" value="{{ date('d/m/Y', $timeInt) }}" type="text"/>
                                        @if ($i == 1)
                                            <span class="input-group-btn btn-add-input">
                                                <button class="btn btn-info btn-flat btn-add-more" onclick="btn_add_more(this)" type="button"><i class="fa fa-plus-square fa-2x"></i></button>
                                            </span>
                                        @else
                                            <span class="input-group-btn btn-add-input">
                                                <button class="btn btn-info btn-flat btn-remove-more" onclick="btn_remove_more(this)" type="button"><i class="fa fa-minus-square fa-2x"></i></button>
                                            </span>
                                        @endif
                                        <?$i++;?>
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input class="form-control date-picker" name="proh_day_deny[]" type="text"/>
                                    <span class="input-group-btn btn-add-input">
                                        <button class="btn btn-info btn-flat btn-add-more" onclick="btn_add_more(this)" type="button"><i class="fa fa-plus-square fa-2x"></i></button>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
            @if ($infoPromo->proh_type == App\Models\Components\Promotion::TYPE_PROMO_EARLY)
                @include('components.modules.hotel_promotion.edit-promo-early')
            @elseif ($infoPromo->proh_type == App\Models\Components\Promotion::TYPE_PROMO_LASTMINUTE)
                @include('components.modules.hotel_promotion.edit-promo-last-minutes')
            @elseif ($infoPromo->proh_type == App\Models\Components\Promotion::TYPE_PROMO_CUSTOM)
                @include('components.modules.hotel_promotion.edit-promo-custom')
            @endif

            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Mức giảm giá</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label>Loại phòng và loại giá</label>
                        <table class="table table-striped vertical-middle">
                            <tbody>
                                <tr>
                                    <th>
                                        <div class="checkbox">
                                            <label>
                                                {!! Form::checkbox('check_all_room_custom', null, true, ['id' => 'checkAllRoomCustom', 'onchange' => "checkbox_all(this, 'custom', 'checkbox-custom')"]) !!}
                                                Loại phòng
                                            </label>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="checkbox">
                                            <label>
                                                {!! Form::checkbox('check_all_rate_custom', null, true, ['id' => 'checkAllRateCustom', 'onchange' => "checkbox_all(this, 'custom', 'checkbox-custom', 'checkbox-rate-custom')"]) !!}
                                                Hệ thống giá
                                            </label>
                                        </div>
                                    </th>
                                </tr>

                                @if(!$dataRoomByHotel->isEmpty())
                                    <?
                                        $rateRoomPromoInfo = [];
                                        $promoRoomRateId   = [];
                                    ?>
                                    @foreach ($dataRoomByHotel->load('ratePlans') as $roomInfo)
                                    <?
                                        $check_room_apply[$roomInfo->rom_id] = 0;

                                        if (in_array($roomInfo->rom_id, $listRoomID)) {
                                            $check_room_apply[$roomInfo->rom_id] = 1;
                                        }
                                        $dataRateInfo = $roomInfo->ratePlans()->where('rap_active', '=', ACTIVE)
                                                                  ->where('rap_delete', '=', NO_ACTIVE)
                                                                  ->where('rap_parent_id', '=', NO_ACTIVE)
                                                                  ->where('rap_type_price', '=', $infoPromo->proh_promo_type)
                                                                  ->get();
                                    ?>
                                        @if (!$dataRateInfo->isEmpty())
                                            <tr>
                                                <td>
                                                    <div class="checkbox checkbox-custom checkbox-room-type-custom checkbox-rate-custom-{{ $roomInfo->rom_id }}">
                                                        <label>
                                                            {!! Form::checkbox($prefix . '_room_id[]', $roomInfo->rom_id, (in_array($roomInfo->rom_id, $listRoomID)) ? true : false, ['id' => 'checkAllRateCustom', 'onchange' => "checkbox_all(this, 'custom', 'checkbox-room-custom-$roomInfo->rom_id')"]) !!}
                                                            {{ $roomInfo->rom_name }}
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    @foreach($dataRateInfo as $rateInfo)
                                                    <?
                                                        $check_rate_apply[$roomInfo->rom_id][$rateInfo->rap_id] = 0;

                                                        if (isset($listRoomRate[$roomInfo->rom_id])
                                                            && in_array($rateInfo->rap_id, $listRoomRate[$roomInfo->rom_id])) {
                                                            $check_rate_apply[$roomInfo->rom_id][$rateInfo->rap_id] = 1;
                                                            $rateRoomPromoInfo[$roomInfo->rom_id][$rateInfo->rap_id] = $rateInfo->rap_id;
                                                        }
                                                    ?>
                                                        <div class="checkbox checkbox-custom checkbox-rate-custom checkbox-room-custom-{{ $roomInfo->rom_id }}">
                                                            <label>
                                                                {!! Form::checkbox($prefix . '_rate_plan_id' . $roomInfo->rom_id . $rateInfo->rap_id, $rateInfo->rap_id, (isset($check_rate_apply[$roomInfo->rom_id][$rateInfo->rap_id]) && $check_rate_apply[$roomInfo->rom_id][$rateInfo->rap_id]) ? true : false, ['id' => 'checkAllRateCustom', 'class' => 'input_room_show_custom', 'data-rate-id' => $rateInfo->rap_id, 'data-room-id' => $roomInfo->rom_id, 'onchange' => "checkbox_all(this, 'custom', 'checkbox-rate-custom-$roomInfo->rom_id', '', 1)"]) !!}
                                                                {{ $rateInfo->rap_title }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        @if ($infoPromo->proh_promo_type == App\Models\Components\Promotion::TYPE_PROMO_TA)
                            <div class="form-group col-sm-8">
                                <p><label>Áp dụng mức giá Khuyến mãi</label></p>
                            </div>
                            <div class="col-sm-12">
                                <table class="table text-center vertical-middle">
                                    <tr>
                                        <th>Tên phòng</th>
                                        <th>Tên Giá</th>
                                        @for($i = 2; $i <= 7; $i++)
                                            <th>Thứ {{ $i }}</th>
                                        @endfor
                                        <th>Chủ nhật</th>
                                    </tr>

                                    @if(!$dataRoomByHotel->isEmpty())
                                        @foreach ($dataRoomByHotel as $roomInfo)
                                            @if (!$roomInfo->ratePlans->isEmpty())
                                                @foreach($roomInfo->ratePlans as $rateInfo)
                                                    @if ($rateInfo->rap_type_price == 0
                                                    && $rateInfo->rap_parent_id == 0
                                                    && $rateInfo->rap_active == ACTIVE
                                                    && $rateInfo->rap_delete == NO_ACTIVE)
                                                        <tr class="input-price input_price_apply_custom" id="{{ 'input_price_custom' . $roomInfo->rom_id . $rateInfo->rap_id }}" {!! (isset($check_rate_apply[$roomInfo->rom_id][$rateInfo->rap_id]) && $check_rate_apply[$roomInfo->rom_id][$rateInfo->rap_id]) ? '' : 'style="display: none"' !!}>
                                                            <td>{{ $roomInfo->rom_name }}</td>
                                                            <td>{{ $rateInfo->rap_title }}</td>
                                                            @for ($i = 1; $i <= 7; $i++)
                                                                <td>
                                                                    {!! Form::text($prefix . '_price_contract_ta_promo_' . $roomInfo->rom_id . $rateInfo->rap_id . '[' . $i . ']', (isset($dataPriceTa[$roomInfo->rom_id][$rateInfo->rap_id][$i]) && $dataPriceTa[$roomInfo->rom_id][$rateInfo->rap_id][$i] != "") ? number_format($dataPriceTa[$roomInfo->rom_id][$rateInfo->rap_id][$i]) : '', ['class' => 'form-control', 'placeholder' => 'Giá nhập']) !!}
                                                                </td>
                                                            @endfor
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                </table>
                            </div>
                        @else
                            <div class="form-group col-sm-6">
                                <p><label>Áp dụng mức giảm giá</label></p>
                                <table class="vertical-middle">
                                    <tr>
                                        <td class="col-sm-1">Đơn vị</td>
                                        <td class="col-sm-2">
                                            {!! Form::select($prefix . '_proh_discount_type', $dataTypeDiscount, $infoPromo->proh_discount_type, ['class' => 'form-control']) !!}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-sm-12" {!! $infoPromo->proh_discount_type == App\Models\Components\Promotion::TYPE_DISCOUNT_FREE_NIGHT ? 'style="display:none"' : '' !!}>
                                <table class="table text-center vertical-middle">
                                    @for($i = 2; $i <= 7; $i++)
                                        <th>Thứ {{ $i }}</th>
                                    @endfor
                                    <th>Chủ nhật</th>
                                    <tr>
                                        @foreach($infoPromo->proh_promotion_info as $dayOfWeek => $discount)
                                            @if ($infoPromo->proh_discount_type == App\Models\Components\Promotion::TYPE_DISCOUNT_MONEY)
                                                <td>
                                                    {!! Form::text($prefix . '_price_contract_ota_promo[' . $dayOfWeek . ']', number_format($discount), ['class' => 'form-control input-price']) !!}
                                                </td>
                                            @else
                                                <td>
                                                    {!! Form::text($prefix . '_price_contract_ota_promo[' . $dayOfWeek . ']', $discount, ['class' => 'form-control input-price']) !!}
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>
                                </table>
                            </div>
                            <div class="col-sm-12 option-free-night-custom" {!! $infoPromo->proh_discount_type != App\Models\Components\Promotion::TYPE_DISCOUNT_FREE_NIGHT ? 'style="display:none"' : '' !!}>
                                <div class="col-sm-3">
                                    <label>Số đêm ở</label>
                                    {!! Form::selectRange($prefix . '_proh_free_night_num', 1, 7, $infoPromo->proh_free_night_num, ['class' => 'form-control']); !!}
                                </div>
                                <div class="col-sm-3">
                                    <label>Số đêm tặng</label>
                                    {!! Form::selectRange($prefix . '_proh_free_night_discount', 1, 7, $infoPromo->proh_free_night_discount, ['class' => 'form-control']); !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Chính sách hủy</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group col-sm-12">
                            <div class="radio">
                                <label>
                                    {!! Form::radio('proh_cancel_policy', '1', $infoPromo->proh_cancel_policy ? true : false) !!}
                                    Giữ nguyên chính sách hủy của đơn giá áp dụng
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    {!! Form::radio('proh_cancel_policy', '0', !$infoPromo->proh_cancel_policy ? true : false) !!}
                                    Không hoàn hủy
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-sm-5">
                            <p><label>Điều kiện giới hạn</label></p>
                            <table class="table vertical-middle">
                                <tr>
                                    <td>Ở tối thiểu: </td>
                                    <td>
                                        {!! Form::text($prefix . '_proh_min_night', $infoPromo->proh_min_night, ['class' => 'form-control']) !!}
                                    </td>
                                    <td>đêm</td>
                                </tr>
                                <tr>
                                    <td>Ở tối đa: </td>
                                    <td>
                                        {!! Form::text($prefix . '_proh_max_night', $infoPromo->proh_max_night, ['class' => 'form-control']) !!}
                                    </td>
                                    <td>đêm</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->

                <div class="box-footer">
                    <a href="{{ route('hotel-promo-list', $infoPromo->proh_hotel) }}"><button class="btn btn-info pull-left" type="button"><i class="fa fa-arrow-circle-left "></i> Quay lại</button></a>
                    <button class="btn btn-info pull-right" type="submit">Cập nhật</button>
                    {!! Form::hidden('proh_promo_type', $infoPromo->proh_promo_type) !!}
                </div>
            </div>
    </div>
</div>

@stop

@section('js-footer')
    <script src="{{ asset('assets/mytour/js/mytour.input_currency.js') }}"></script>
    <script src="{{ asset('assets/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript">
        //Date range picker
        $('.date-range').daterangepicker({
            format: 'DD/MM/YYYY'
        });

        $('.time-picker').datetimepicker({
            format: 'HH:mm',
        });

        function date_picker()
        {
            $(".day-not-apply .date-picker").datepicker({
                format: 'dd/mm/yyyy',
            });
        }

        $("input[name=day_apply_every]").click(function() {
            if ($(this).val() == 1) {
                $("#collapseOne").collapse('hide');
            } else {
                $("#collapseOne").collapse('show');
            }
        })

        function btn_add_more(obj)
        {
            var htmlInputAdd = '<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input class="form-control date-picker" name="proh_day_deny[]" type="text" data-items="8"/><span class="input-group-btn btn-add-input"><button class="btn btn-info btn-flat btn-remove-more" onclick="btn_remove_more(this)" type="button"><i class="fa fa-minus-square fa-2x"></i></button></span></div>';
            $(obj).parents(".day-not-apply").append(htmlInputAdd);
            //Call plugin datepicker for input add new
            date_picker();
        }


        function btn_remove_more(obj)
        {
            $(obj).parents('div.input-group').remove();
        }


        function checkbox_all (obj, prefix, name_class_checked, name_class_no_checked = '', not_check = 0)
        {
            if (not_check == 1) {
                var rom_id  = $(obj).data('room-id');
                var rate_id = $(obj).data('rate-id');

                if ($(obj).is( ":checked" )) {
                    $("." + name_class_checked + " input:checkbox").prop('checked', $(obj).prop("checked"));
                    $("#input_price_" + prefix + rom_id + rate_id).show();
                } else {
                    $("#input_price_" + prefix + rom_id + rate_id).hide();
                }
            } else {
                if (name_class_no_checked != '') {
                    if ($(obj).is( ":checked" )) {
                        $("." + name_class_checked + " input:checkbox").prop('checked', $(obj).prop("checked"));
                    } else {
                        $("." + name_class_no_checked + " input:checkbox").prop('checked', $(obj).prop("checked"));
                    }
                    showHideAllInput(obj, prefix);
                } else {
                    $("." + name_class_checked + " input:checkbox").prop('checked', $(obj).prop("checked"));
                    $("." + name_class_checked).each(function (){
                        var rom_id  = $(this).find('input:checkbox').data('room-id');
                        var rate_id = $(this).find('input:checkbox').data('rate-id');
                        if ($(obj).is( ":checked" )) {
                            $("#input_price_" + prefix + rom_id + rate_id).show();
                        } else {
                            $("#input_price_" + prefix + rom_id + rate_id).hide();
                        }
                    });
                }
            }
        }

        function showHideAllInput(obj, prefix)
        {
            if ($(obj).is( ":checked" )) {
                $(".input_price_apply_" + prefix).show();
            } else {
                $(".input_price_apply_" + prefix).hide();
            }
        }

        //Call plugin datepicker
        date_picker();
    </script>
@stop



