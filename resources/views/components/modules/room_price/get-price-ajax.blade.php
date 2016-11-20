@if(!empty($dataRoomInfo))
    <div class="header-price col-md-offset-3">
        <ul>
            <li>
                <ul>
                    <li>
                        <button class="btn btn-default btn-prev" onclick="getPriceByDate({!! $time_checkin - 7 * 86400 !!}, {!! $time_checkin - 86400 !!})" type="button"><i class="fa fa-arrow-circle-left fa-2x"></i></button>
                    </li>
                    <li style="padding: 0 86px">
                        <h2 class="no-margin week-time">{!! date('d/m/Y', $time_checkin) !!} - {!! date('d/m/Y', $time_checkout) !!}</h2>
                    </li>
                    <li>
                        <button class="btn btn-default btn-next" onclick="getPriceByDate({!! $time_checkout + 86400 !!}, {!! $time_checkout + 7 * 86400 !!})" type="button"><i class="fa fa-arrow-circle-right fa-2x"></i></button>
                    </li>
                </ul>
            </li>
            <li>*Giá đã bao gồm thuế và dịch vụ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; *Đơn vị: VNĐ</li>
            @if($typeHotel)
                <li class="col-sm-offset-1">
                    <ul>
                        <li>
                            <div class="form-horizontal col-sm-12">
                                <div class="form-group">
                                    <label class="control-label pull-left" for="commission-ota">Chiết khấu OTA</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="commission-ota" name="commission_ota" value="{{ $commission_ota }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form-horizontal col-sm-12">
                                <div class="form-group">
                                    <label class="control-label pull-left" for="markup-ta">Mark-up TA</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="markup-ta" name="markup_ta" value="{{ $mark_up }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
            @else
                <li class="col-sm-offset-4">
                    <ul>
                        <li>
                            <div class="form-horizontal">
                                <div class="form-group col-sm-12">
                                    <label class="control-label pull-left" for="markup-ta">Mark-up TA</label>
                                    <div class="col-sm-6">
                                        <input type="text" id="markup-ta" name="markup_ta" value="{{ $mark_up }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>
    </div>
    <table class="table text-center vertical-middle">
        <tbody>
            <!-- show alert cap nhat thong tin -->
            @include('layouts.includes.show-alert')

            @foreach ($dataRoomInfo as $rID => $roomInfo)
                <tr><td colspan="8" class="no-border">&nbsp;</td></tr>
                <tr><td colspan="8" class="no-border">&nbsp;</td></tr>
                <tr>
                    <td>&nbsp;</td>
                    @for ($t = $time_checkin; $t <= $time_checkout; $t += 86400)
                        <td>{!! ((int)date('N', $t) <= 6) ? 'Thứ ' . intval(date('N', $t) + 1) : 'Chủ nhật' !!}<p>{!! date('d/m', $t) !!}</p></td>
                    @endfor
                </tr>
                <tr class="bg-light-green">
                    @if (isset($allotmentData[$hotelID][$rID]) && !empty($allotmentData[$hotelID][$rID]))
                        <td width="111px">
                            <h4>{!! $roomInfo !!}</h4>
                        </td>
                        @foreach ($allotmentData[$hotelID][$rID] as $dayInt => $allotmentInfo)
                            <td>
                                @if (!$hotelPms)
                                    <a class="btn btn-app {!! $allotmentInfo['locked'] ? 'text-red' : 'text-green' !!} no-margin" data-value="{!! abs($allotmentInfo['locked'] - 1) !!}" onclick="updateAllotment(this, {!! $rID !!}, {!! $dayInt !!}, {!! $hotelID !!}, {!! $hotelPms !!})">
                                        <i class="fa {!! $allotmentInfo['locked'] ? 'fa-lock' : 'fa-unlock' !!} fa-2x"></i>{!! $allotmentInfo['locked'] ? 'Khóa phòng' : 'Mở phòng' !!}
                                    </a>
                                @elseif($allotmentInfo['locked'])
                                    <a class="btn btn-app {!! $allotmentInfo['locked'] ? 'text-red' : 'text-green' !!} no-margin" data-value="{!! abs($allotmentInfo['locked'] - 1) !!}" onclick="updateAllotment(this, {!! $rID !!}, {!! $dayInt !!}, {!! $hotelID !!}, {!! $hotelPms !!})">
                                        <i class="fa {!! $allotmentInfo['locked'] ? 'fa-lock' : 'fa-unlock' !!} fa-2x"></i>{!! $allotmentInfo['locked'] ? 'Khóa phòng' : 'Mở phòng' !!}
                                    </a>
                                @else
                                    <i>&nbsp;</i>
                                @endif
                            </td>
                        @endforeach
                    @else
                        <td width="111px" colspan="8">
                            <h4>{!! $roomInfo !!}</h4>
                        </td>
                    @endif
                </tr>
                @if (isset($allotmentData[$hotelID][$rID]) && !empty($allotmentData[$hotelID][$rID]))
                    <tr>
                        @if ($typeHotel)
                            <td width="111px">
                                <h5 class="no-margin text-bold">Allotment OTA</h5>
                                @if (!$hotelPms)
                                    <a href="#" data-toggle="modal" data-target="#modalAllotment" data-room ="{!! $rID !!}" data-rate-id ="0" data-hotel="{!! $hotelID !!}" onclick="add_option_modal(this, 'modalAllotment')">Sửa hàng loạt</a>
                                @endif
                            </td>
                            @foreach ($allotmentData[$hotelID][$rID] as $dayInt => $allotmentInfo)
                                <td class="{!! 'allotment_lock_' . $rID . $dayInt !!} {!! $allotmentInfo['locked'] ? 'bg-light-pink' : '' !!}"><input type="text" class="form-control" name="allotment_ota_{!! $rID !!}{!! $dayInt !!}" value="{!! $allotmentInfo['num_allot'] !!}"></td>
                            @endforeach
                        @endif
                    </tr>
                @endif
                @if (isset($ratePlanInfo[$rID]) && !empty($ratePlanInfo[$rID]))
                <?$i = 1;?>
                    @foreach ($ratePlanInfo[$rID] as $rateID => $rateInfo)
                        <tr>
                            <td colspan="8" class="no-padding">
                                <div class="box {{ $i == 1 ? '' : 'collapsed-box' }} no-border">
                                    <div class="box-header bg-lightblue">
                                        <h5 class="col-xs-1 no-padding no-margin text-bold">{!! $rateInfo['title'] !!}</h5>
                                        <div class="col-xs-2">
                                            {!! $rateInfo['type_price'] ? '*Giá khách lẻ (OTA)' : '*Giá đại lý (TA)' !!}
                                        </div>
                                        @if (!$rateInfo['type_price'])
                                            <div class="col-xs-1">
                                                <div class="checkbox no-margin">
                                                    <label>
                                                        <input type="checkbox" data-hidden-price="{{ $rateInfo['hidden_price'] }}" data-type-hidden="hidden-price" onclick="hiddenPrice(this, {!! $rateInfo['rrp_id'] !!})" id="rap_hidden_price_{!! $rateInfo['rrp_id'] !!}" {!! $rateInfo['hidden_price'] ? 'checked' : '' !!}> Ẩn giá
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-xs-2">
                                                <div class="checkbox no-margin">
                                                    <label>
                                                        <input type="checkbox" data-hidden-price="{{ $rateInfo['price_email'] }}" data-type-hidden="price-email" onclick="hiddenPrice(this, {!! $rateInfo['rrp_id'] !!})" id="rap_price_email_{!! $rateInfo['rrp_id'] !!}" {!! $rateInfo['price_email'] ? 'checked' : '' !!}> Báo giá qua email
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="box-tools">
                                            <button data-widget="collapse" class="btn btn-box-tool"><i class="fa {{ $i == 1 ? 'fa-minus' : 'fa-plus' }} fa-lg"></i></button>
                                        </div><!-- /.box-tools -->
                                    </div><!-- /.box-header -->
                                    <div class="box-body">
                                        <table class="table">
                                            @if ($rateInfo['type_price'])
                                                @if (isset($priceData[$hotelID][$rID][$rateID]) && !empty($priceData[$hotelID][$rID][$rateID]))
                                                    @foreach ($priceData[$hotelID][$rID][$rateID] as $personType => $pricePersonInfo)
                                                        <tr>
                                                            <td width="111px">
                                                                <p class="no-margin">Giá vào {!! $personType == 1 ? '1 người' : ''  !!}</p>
                                                            </td>
                                                                @foreach($pricePersonInfo as $dayInt => $priceInfo)
                                                                    <td class="{!! 'allotment_lock_' . $rID . $dayInt !!} {!! $priceInfo['locked'] ? 'bg-light-pink' : '' !!}"><input type="text" class="form-control" value='{!! number_format($priceInfo["ota_in"]) !!}' disabled=""></td>
                                                                @endforeach
                                                        </tr>
                                                        <tr class="input-price">
                                                             <td width="111px">
                                                                <p class="no-margin">Giá bán {!! $personType == 1 ? '1 người' : ''  !!}</p>
                                                                @if (!$hotelPms)
                                                                    <a href="#" data-toggle="modal" data-target="#modalPrice" data-rate-id="{!! $rateID !!}" data-hotel="{{ $hotelID }}" data-person-type="{!! $personType !!}" data-room="{!! $rID !!}" onclick="add_option_modal(this, 'modalPrice')">Sửa hàng loạt</a>
                                                                @endif
                                                            </td>
                                                            @foreach($pricePersonInfo as $dayInt => $priceInfo)
                                                                <td class="{!! 'allotment_lock_' . $rID . $dayInt !!} {!! $priceInfo['locked'] ? 'bg-light-pink' : '' !!}"><input type="text" class="form-control" name="price_ota_out_{!! $rID !!}{!! $rateID !!}{!! $personType !!}{!! $dayInt !!}" value='{!! number_format($priceInfo["ota_out"]) !!}'></td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @else
                                                @if (isset($priceData[$hotelID][$rID][$rateID]) && !empty($priceData[$hotelID][$rID][$rateID]))
                                                    @foreach ($priceData[$hotelID][$rID][$rateID] as $personType => $pricePersonInfo)
                                                        <tr>
                                                            <td width="111px">
                                                                <p class="no-margin">Giá vào {!! $personType == 1 ? '1 phòng' : ''  !!}</p>
                                                                <a href="#" data-toggle="modal" data-target="#modalPriceContract" data-rate-id="{!! $rateID !!}" data-hotel="{{ $hotelID }}" data-person-type="{!! $personType !!}" data-room="{!! $rID !!}" onclick="add_option_modal(this, 'modalPriceContract')">Sửa hàng loạt</a>
                                                            </td>
                                                            @foreach($pricePersonInfo as $dayInt => $priceInfo)
                                                                <td class="{!! 'allotment_lock_' . $rID . $dayInt !!} {!! $priceInfo['locked'] ? 'bg-light-pink' : '' !!}"><input type="text" class="form-control" name="price_ta_in_{!! $rID !!}{!! $rateID !!}{!! $personType !!}{!! $dayInt !!}" value='{!! number_format($priceInfo["ta_in"]) !!}'></td>
                                                            @endforeach
                                                        </tr>
                                                        <tr class="input-price">
                                                            <td width="111px">
                                                                <p class="no-margin">Giá bán {!! $personType == 1 ? '1 phòng' : ''  !!}</p>
                                                            </td>
                                                            @foreach($pricePersonInfo as $dayInt => $priceInfo)
                                                                <td class="{!! 'allotment_lock_' . $rID . $dayInt !!} {!! $priceInfo['locked'] ? 'bg-light-pink' : '' !!}"><input type="text" class="form-control" value='{!! number_format($priceInfo["ta_out"]) !!}' disabled=""></td>
                                                            @endforeach
                                                        </tr>
                                                     @endforeach
                                                @endif
                                            @endif
                                            <!-- Show children Rate Plan -->
                                            @if (isset($ratePricePromo[$rID][$rateID]) && !empty($ratePricePromo[$rID][$rateID]))
                                                @foreach ($ratePricePromo[$rID][$rateID] as $pID => $ratePromo)
                                                    <tr>
                                                        <td colspan="8" style="background-color: #e7f2f2" class="text-align-left text-bold">{!! $ratePromo['promo_info']['title'] !!}</td>
                                                    </tr>
                                                    @if ($ratePromo['promo_info']['type_promo'])
                                                        @if (isset($ratePromo['promo_price']) && !empty($ratePromo['promo_price']))
                                                            @foreach ($ratePromo['promo_price'] as $personType => $pricePersonInfo)
                                                                <tr>
                                                                    <td width="111px">
                                                                        <p class="no-margin">Giá vào {!! $personType == 1 ? '1 người' : ''  !!}</p>
                                                                    </td>
                                                                        @foreach($pricePersonInfo as $dayInt => $priceInfo)
                                                                            <td class="{!! 'allotment_lock_' . $rID . $dayInt !!} {!! $priceInfo['locked'] ? 'bg-light-pink' : '' !!}"><input type="text" disabled="" class="form-control" value='{!! number_format($priceInfo["ota_in"]) !!}' disabled=""></td>
                                                                        @endforeach
                                                                </tr>
                                                                <tr class="input-price">
                                                                     <td width="111px">
                                                                        <p class="no-margin">Giá bán {!! $personType == 1 ? '1 người' : ''  !!}</p>
                                                                    </td>
                                                                    @foreach($pricePersonInfo as $dayInt => $priceInfo)
                                                                        <td class="{!! 'allotment_lock_' . $rID . $dayInt !!} {!! $priceInfo['locked'] ? 'bg-light-pink' : '' !!}"><input type="text" disabled="" class="form-control"  value='{!! number_format($priceInfo["ota_out"]) !!}'></td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    @else
                                                        @if (isset($ratePromo['promo_price']) && !empty($ratePromo['promo_price']))
                                                            @foreach ($ratePromo['promo_price'] as $personType => $pricePersonInfo)
                                                                <tr>
                                                                    <td width="111px">
                                                                        <p class="no-margin">Giá vào {!! $personType == 1 ? '1 phòng' : ''  !!}</p>
                                                                    </td>
                                                                    @foreach($pricePersonInfo as $dayInt => $priceInfo)
                                                                        <td class="{!! 'allotment_lock_' . $rID . $dayInt !!} {!! $priceInfo['locked'] ? 'bg-light-pink' : '' !!}"><input type="text" disabled="" class="form-control" value='{!! number_format($priceInfo["ta_in"]) !!}'></td>
                                                                    @endforeach
                                                                </tr>
                                                                <tr class="input-price">
                                                                    <td width="111px">
                                                                        <p class="no-margin">Giá bán {!! $personType == 1 ? '1 phòng' : ''  !!}</p>
                                                                    </td>
                                                                    @foreach($pricePersonInfo as $dayInt => $priceInfo)
                                                                        <td class="{!! 'allotment_lock_' . $rID . $dayInt !!} {!! $priceInfo['locked'] ? 'bg-light-pink' : '' !!}"><input type="text" disabled="" class="form-control" value='{!! number_format($priceInfo["ta_out"]) !!}' disabled=""></td>
                                                                    @endforeach
                                                                </tr>
                                                             @endforeach
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        </table>
                                    </div><!-- /.box-body -->
                                </div>
                            </td>
                        </tr>
                        <?$i++;?>
                    @endforeach
                @endif
            @endforeach
            <input type="hidden" name="hotel_id" value="{{ $hotelID }}">
            <input type="hidden" name="room_rate_info" value="{{ json_encode($ratePlanInfo) }}">
            <input type="hidden" name="time_checkin" value="{{ $time_checkin }}">
            <input type="hidden" name="time_checkout" value="{{ $time_checkout }}">
        </tbody>
    </table>
@endif