<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Thời gian đặt phòng</h3>
    </div>
    <div class="box-body">
        <div class="form-group">
            <label>Khoảng thời gian đặt phòng</label>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                {!! Form::text('date_range_book', null, ['class' => 'form-control pull-right date-range']) !!}
            </div>
            <div class="radio">
                <label>
                    {!! Form::radio('day_apply_every', 1, true) !!}
                    Áp dụng mọi thời điểm thuộc khoảng thời gian đặt phòng
                </label>
            </div>
            <div class="radio">
                <label>
                    {!! Form::radio('day_apply_every', 0) !!}
                    Vào ngày giờ cụ thể
                </label>
            </div>
            <div {!! (old('day_apply_every') == 0 && old('day_apply_every') != null) ? 'class="bg-gray collapse in" aria-expanded="true"' : 'class="bg-gray collapse"' !!} id="collapseOne">
                <div class="box-body">
                    <div class="form-group checkbox-week">
                        <p class="no-margin">Chỉ áp dụng cho các ngày cụ thể trong tuần</p>
                        @for($i = 1; $i <= 6; $i++)
                            <div class="pull-left">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('proh_day_apply[' . $i . ']', 1) !!}
                                        Thứ {{ $i + 1 }}
                                    </label>
                                </div>
                            </div>
                        @endfor
                        <div class="pull-left">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('proh_day_apply[7]', 1) !!}
                                    Chủ nhật
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group">
                        <p>Chỉ áp dụng cho giờ cụ thể trong ngày</p>
                        <div class="input-group col-sm-3 pull-left">
                            <div class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                            {!! Form::text('time_start_apply', null, ['class' => 'form-control pull-right time-picker']) !!}
                        </div>
                        <div class="input-group col-sm-4">
                            <label class="col-sm-1">Đến</label>
                            <div class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                            {!! Form::text('time_finish_apply', null, ['class' => 'form-control pull-right time-picker']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
                            <div class="checkbox checkbox-custom">
                                <label>
                                    {!! Form::checkbox('check_all_room_custom', null, null, ['id' => 'checkAllRoomCustom', 'onchange' => "checkbox_all(this, 'custom', 'checkbox-custom')"]) !!}
                                    Loại phòng
                                </label>
                            </div>
                        </th>
                        <th>
                            <div class="checkbox checkbox-custom">
                                <label>
                                    {!! Form::checkbox('check_all_rate_custom', null, null, ['id' => 'checkAllRateCustom', 'onchange' => "checkbox_all(this, 'custom', 'checkbox-custom', 'checkbox-rate-custom')"]) !!}
                                    Hệ thống giá
                                </label>
                            </div>
                        </th>
                    </tr>
                    @if(!$dataRoomByHotel->isEmpty())
                        @foreach ($dataRoomByHotel as $roomInfo)
                        <?
                            $dataRateInfo = $roomInfo->ratePlans()->where('rap_active', '=', ACTIVE)
                                                                  ->where('rap_delete', '=', NO_ACTIVE)
                                                                  ->where('rap_parent_id', '=', NO_ACTIVE)
                                                                  ->where('rap_type_price', '=', $typePromo)
                                                                  ->get();
                        ?>
                            @if (!$dataRateInfo->isEmpty())
                                <tr>
                                    <td>
                                        <div class="checkbox checkbox-custom checkbox-room-type-custom checkbox-rate-custom-{{ $roomInfo->rom_id }}">
                                            <label>
                                                {!! Form::checkbox($prefix . 'room_id[]', $roomInfo->rom_id, null, ['id' => 'checkAllRateCustom', 'onchange' => "checkbox_all(this, 'custom', 'checkbox-room-custom-$roomInfo->rom_id')"]) !!}
                                                {{ $roomInfo->rom_name }}
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        @foreach($dataRateInfo as $rateInfo)
                                            <div class="checkbox checkbox-custom checkbox-rate-custom checkbox-room-custom-{{ $roomInfo->rom_id }}">
                                                <label>
                                                    {!! Form::checkbox($prefix . 'rate_plan_id' . $roomInfo->rom_id . $rateInfo->rap_id, $rateInfo->rap_id, null, ['id' => 'checkAllRateCustom', 'class' => 'input_room_show_custom', 'data-rate-id' => $rateInfo->rap_id, 'data-room-id' => $roomInfo->rom_id, 'onchange' => "checkbox_all(this, 'custom', 'checkbox-rate-custom-$roomInfo->rom_id', '', 1)"]) !!}
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
            @if($typePromo == App\Models\Components\Promotion::TYPE_PROMO_OTA)
                <div class="form-group col-sm-6">
                    <p><label>Áp dụng mức giảm giá</label></p>
                    <table class="vertical-middle">
                        <tr>
                            <td class="col-sm-1">Đơn vị</td>
                            <td class="col-sm-2">
                                {!! Form::select($prefix . 'proh_discount_type', $dataTypeDiscount, null,['class' => 'form-control', 'onchange' => "show_option_free_night(this, 'custom')"]) !!}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-12 option-percent-custom">
                    <table class="table text-center vertical-middle">
                        @for($i = 2; $i <= 7; $i++)
                            <th>Thứ {{ $i }}</th>
                        @endfor
                        <th>Chủ nhật</th>
                        <tr>
                            @for($i = 1; $i <= 7; $i++)
                                <td>
                                    {!! Form::text($prefix . 'price_contract_ota_promo[' . $i . ']', null, ['class' => 'form-control input-price']) !!}
                                </td>
                            @endfor
                        </tr>
                    </table>
                </div>
                <div class="col-sm-12 option-free-night-custom" style="display: none">
                    <div class="col-sm-3">
                        <label>Số đêm ở</label>
                        {!! Form::selectRange($prefix . 'proh_free_night_num', 1, 7, null, ['class' => 'form-control']); !!}
                    </div>
                    <div class="col-sm-3">
                        <label>Số đêm tặng</label>
                        {!! Form::selectRange($prefix . 'proh_free_night_discount', 1, 7, null, ['class' => 'form-control']); !!}
                    </div>
                </div>
            @else
                <div class="form-group col-sm-6">
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
                                <?
                                    $dataRateInfo = $roomInfo->ratePlans()->where('rap_active', '=', ACTIVE)
                                                                          ->where('rap_delete', '=', NO_ACTIVE)
                                                                          ->where('rap_parent_id', '=', NO_ACTIVE)
                                                                          ->where('rap_type_price', '=', NO_ACTIVE)
                                                                          ->get();
                                ?>
                                @if (!$dataRateInfo->isEmpty())
                                    @foreach($dataRateInfo as $rateInfo)
                                        <tr class="input-price input_price_apply_custom" id="{{ 'input_price_custom' . $roomInfo->rom_id . $rateInfo->rap_id }}" {!! old('custom_rate_plan_id' . $roomInfo->rom_id . $rateInfo->rap_id) == null ? 'style="display: none"' : '' !!}>
                                            <td>{{ $roomInfo->rom_name }}</td>
                                            <td>{{ $rateInfo->rap_title }}</td>
                                            @for($i = 1; $i <= 7; $i++)
                                                <td>
                                                    {!! Form::text($prefix . 'price_contract_ta_promo_' . $roomInfo->rom_id . $rateInfo->rap_id . '[' . $i . ']', null, ['class' => 'form-control']) !!}
                                                </td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
