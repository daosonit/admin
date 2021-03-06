<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Thời gian đặt phòng</h3>
    </div>
    <div class="box-body">
        <div class="col-sm-8">
            <table class="table vertical-middle">
                <tr>
                    <td>Khách cần đặt trước ngày nhận phòng ít nhất</td>
                    <td>
                        {!! Form::text('proh_min_day_before', null, ['class' => 'form-control']) !!}
                    </td>
                    <td>ngày</td>
                </tr>
            </table>
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
                            <div class="checkbox checkbox-early">
                                <label>
                                    {!! Form::checkbox('check_all_room_early', null, null, ['id' => 'checkAllRoomEarly', 'onchange' => "checkbox_all(this, 'early', 'checkbox-early')"]) !!}
                                    Loại phòng
                                </label>
                            </div>
                        </th>
                        <th>
                            <div class="checkbox checkbox-early">
                                <label>
                                    {!! Form::checkbox('check_all_rate_early', null, null, ['id' => 'checkAllRateEarly', 'onchange' => "checkbox_all(this, 'early', 'checkbox-early', 'checkbox-rate-early')"]) !!}
                                    Hệ thống giá
                                </label>
                            </div>
                        </th>
                    </tr>
                    @if(!$dataRoomByHotel->isEmpty())
                        @foreach ($dataRoomByHotel as $roomInfo)
                            <tr>
                                <td>
                                    <div class="checkbox checkbox-early checkbox-room-type-early checkbox-rate-early-{{ $roomInfo->rom_id }}">
                                        <label>
                                            {!! Form::checkbox($prefix . 'room_id[]', $roomInfo->rom_id, null, ['id' => 'checkAllRateEarly', 'onchange' => "checkbox_all(this, 'early', 'checkbox-room-early-$roomInfo->rom_id')"]) !!}
                                            {{ $roomInfo->rom_name }}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <?
                                        $dataRateInfo = $roomInfo->ratePlans()->where('rap_active', '=', ACTIVE)
                                                                              ->where('rap_delete', '=', NO_ACTIVE)
                                                                              ->where('rap_parent_id', '=', NO_ACTIVE)
                                                                              ->where('rap_type_price', '=', $typePromo)
                                                                              ->get();
                                    ?>
                                    @if (!$dataRateInfo->isEmpty())
                                        @foreach($dataRateInfo as $rateInfo)
                                            <div class="checkbox checkbox-early checkbox-rate-early checkbox-room-early-{{ $roomInfo->rom_id }}">
                                                <label>
                                                    {!! Form::checkbox($prefix . 'rate_plan_id' . $roomInfo->rom_id . $rateInfo->rap_id, $rateInfo->rap_id, null, ['id' => 'checkAllRateEarly', 'class' => 'input_room_show_early', 'data-rate-id' => $rateInfo->rap_id, 'data-room-id' => $roomInfo->rom_id, 'onchange' => "checkbox_all(this, 'early', 'checkbox-rate-early-$roomInfo->rom_id', '', 1)"]) !!}
                                                    {{ $rateInfo->rap_title }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
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
                                {!! Form::select($prefix . 'proh_discount_type', $dataTypeDiscount, null,['class' => 'form-control', 'onchange' => "show_option_free_night(this, 'early')"]) !!}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-12 option-percent-early">
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
                <div class="col-sm-12 option-free-night-early" style="display: none">
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
                                        <tr class="input-price input_price_apply_early" id="{{ 'input_price_early' . $roomInfo->rom_id . $rateInfo->rap_id }}" {!! old('early_rate_plan_id' . $roomInfo->rom_id . $rateInfo->rap_id) == null ? 'style="display: none"' : '' !!}>
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
