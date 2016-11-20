@extends('layouts.master')

@section('css')
    <style type="text/css">
        table.vertical-middle,
        table.vertical-middle td,
        table.vertical-middle th {
            vertical-align: middle !important;
            font-size: 14px;
        }
        .modal-dialog.large {
            width: 750px;
        }
        .label {font-size: 100%}
        .checkbox-week .checkbox{padding-right: 15px}
        .checkbox-week .pull-left:last-child .checkbox{padding-right: 0}
        .text-align-left{text-align: left !important}
        .text-align-right{text-align: right !important}
        .bg-light-green{background-color: #CBE8BA}
        .bg-light-green a.btn-app{background: none}
        .bg-light-green a.btn-app:hover{border: 0;}
        .bg-lightblue{background-color: #D4E3FC;cursor: pointer;}
        .bg-light-pink{background-color: #F2CAD8}
        .header-price ul{display: table;padding:0;}
        .header-price ul li{list-style: none;text-align: center;padding: 6px 0;}
        .header-price ul li ul li{display: table-cell;vertical-align: middle}
    </style>
@stop

@section('title')
	Thông tin phòng của Khách sạn
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
    	<div class="box box-info">
            {!! Form::open(['route' => 'update-price-form', 'name' => 'room-price', 'method' => 'POST']) !!}
                <div class="box-body" id="input-price">
                    @if(!empty($dataRoomInfo))
                        <div class="header-price col-md-offset-1">
                            <ul style="width: 100%">
                                <li class="col-md-offset-3">
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
                                            <li>
                                                <div class="form-horizontal col-sm-12">
                                                    <div class="form-group">

                                                        <div class="col-sm-12 checkbox no-margin" style="padding-bottom: 10px;">
                                                            <input type="checkbox" id="hot-tax-fee" data-hot-tax-fee="{!! $tax_fee !!}" onclick="checkTaxFee(this, {!! $hotelID !!})" {!! $tax_fee ? 'checked' : '' !!}>
                                                            Giá đã bao gồm thuế và dịch vụ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; *Đơn vị: VNĐ
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
                                            <li>
                                                <div class="form-horizontal col-sm-12">
                                                    <div class="form-group">

                                                        <div class="col-sm-12 checkbox no-margin" style="padding-bottom: 10px;">
                                                            <input type="checkbox" id="hot-tax-fee" data-hot-tax-fee="{!! $tax_fee !!}" onclick="checkTaxFee(this, {!! $hotelID !!})" {!! $tax_fee ? 'checked' : '' !!}>
                                                            Giá đã bao gồm thuế và dịch vụ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; *Đơn vị: VNĐ
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
                </div>
                <div class="box-footer">
                    <button class="btn btn-success pull-right" type="submit">Cập nhật</button>
                </div>
            <!-- /.box-body -->
            {!! Form::close() !!}
            @if (!$hotelPms || $typeHotel == 2)
                <!--=========================== Modal box Allotment ========================-->
                <div class="modal fade" id="modalAllotment" tabindex="-1" role="dialog" aria-labelledby="modalAllotment">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                                <span aria-hidden="true">×</span></button>
                                <h4 class="modal-title">Sửa số lượng Allotment OTA</h4>
                            </div>
                            <div class="modal-body">
                                {!! Form::open(['route' => 'update-allotment-range-time', 'id' => 'allotment-range-time', 'method' => 'POST']) !!}
                                    <div class="form-group col-md-12 msg-alert-allotment">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Khoảng thời gian:</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" id="time-range-allotment" name="time_range_allotment_modal" class="form-control pull-right active date-range">
                                        </div>
                                        <!-- /.input group -->
                                    </div>
                                    <div class="form-group col-md-12 checkbox-week">
                                        @for($i = 1; $i <= 6; $i++)
                                        <div class="pull-left">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="day_apply_allotment_modal[]" checked="" value="{{ $i }}"> Thứ {{ $i + 1 }}
                                                </label>
                                            </div>
                                        </div>
                                        @endfor
                                        <div class="pull-left">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="day_apply_allotment_modal[]" checked="" value="7"> Chủ nhật
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="num_allotment_modal">Nhập số allotment</label>
                                        <input type="text" id="num_allotment_modal" class="form-control" name="num_allot_modal">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="modal-footer">
                                    <button data-dismiss="modal" class="btn btn-default pull-left" type="button">Close</button>
                                    <button class="btn btn-success" type="submit">Cập nhật</button>
                                    <input type="hidden" name="hotel_id" value="0" class="hotel-id" />
                                    <input type="hidden" name="room_id" value="0" class="room-id" />
                                    <input type="hidden" name="rate_id" value="0" class="rate-id" />
                                    <input type="hidden" name="person_type" value="0" class="person-type" />
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>

                <!--=========================== Modal box price contract ========================-->
                <div class="modal fade" id="modalPriceContract" tabindex="-1" role="dialog" aria-labelledby="modalPriceContract">
                    <div class="modal-dialog large" role="document">
                        <div class="modal-content">
                            {!! Form::open(['route' => 'update-price-contract-ta-range-time', 'id' => 'price-contract-range-time', 'method' => 'POST']) !!}
                                <div class="modal-header">
                                    <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                                    <span aria-hidden="true">×</span></button>
                                    <h4 class="modal-title">Nhập giá vào theo khoảng thời gian</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group col-md-12 msg-alert-price-contract">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Khoảng thời gian:</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" id="time-range-price-contract" name="time_range_price_contract" class="form-control pull-right active date-range">
                                        </div>
                                        <!-- /.input group -->
                                    </div>
                                    <div class="form-group col-md-12 checkbox-week">
                                        @for($i = 1; $i <= 6; $i++)
                                        <div class="pull-left">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="day_apply_price_contract[]" checked="" value="{{ $i }}"> Thứ {{ $i + 1 }}
                                                </label>
                                            </div>
                                        </div>
                                        @endfor
                                        <div class="pull-left">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="day_apply_price_contract[]" checked="" value="7"> Chủ nhật
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="num_allotment_modal">Nhập giá</label>
                                        <table class="table table-condensed">
                                            <tbody>
                                                <tr class="input-price">
                                                    <td>
                                                        Giá 1 người
                                                    </td>
                                                    <td>
                                                        <input type="text" placeholder="Giá nhập 1 phòng" class="form-control" id="price-contract-person" name="price_contract_person">
                                                    </td>
                                                    <td>
                                                        <i>Giá phòng khi khách chỉ ở 1 người, nhập bằng 0 khi không có</i>
                                                    </td>
                                                </tr>
                                                <tr class="input-price">
                                                    <td>
                                                        Giá phòng
                                                    </td>
                                                    <td>
                                                        <input type="text" placeholder="Giá nhập" class="form-control" id="price-contract-room" name="price_contract_room">
                                                    </td>
                                                    <td>
                                                        <i>Giá phòng khi khách ở tối đa số lượng người phòng cho phép</i>
                                                    </td>
                                                </tr>
                                            </tbody>
                                      </table>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="modal-footer">
                                    <button data-dismiss="modal" class="btn btn-default pull-left" type="button">Close</button>
                                    <button class="btn btn-success" type="submit">Cập nhật</button>
                                    <input type="hidden" name="hotel_id" value="0" class="hotel-id" />
                                    <input type="hidden" name="room_id" value="0" class="room-id" />
                                    <input type="hidden" name="rate_id" value="0" class="rate-id" />
                                    <input type="hidden" id="commission-ota" name="commission_ota" value="15" class="form-control">
                                    <input type="hidden" id="markup-ta" name="markup_ta" value="16" class="form-control">
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>

                <!--=========================== Modal box Price ========================-->
                <div class="modal fade" id="modalPrice" tabindex="-1" role="dialog" aria-labelledby="modalPrice">
                    <div class="modal-dialog large" role="document">
                        <div class="modal-content">
                            {!! Form::open(['route' => 'update-price-ota-range-time', 'id' => 'price-ota-range-time', 'method' => 'POST']) !!}
                                <div class="modal-header">
                                    <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                                    <span aria-hidden="true">×</span></button>
                                    <h4 class="modal-title">Nhập giá bán theo khoảng thời gian</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group col-md-12 msg-alert-price">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Khoảng thời gian:</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" id="time-range-price" name="time_range_price" class="form-control pull-right active date-range">
                                        </div>
                                        <!-- /.input group -->
                                    </div>
                                    <div class="form-group col-md-12 checkbox-week">
                                        @for($i = 1; $i <= 6; $i++)
                                        <div class="pull-left">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="day_apply_price[]" checked="" value="{{ $i }}"> Thứ {{ $i + 1 }}
                                                </label>
                                            </div>
                                        </div>
                                        @endfor
                                        <div class="pull-left">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="day_apply_price[]" checked="" value="7"> Chủ nhật
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="num_allotment_modal">Nhập giá</label>
                                        <table class="table table-condensed">
                                            <tbody>
                                                <tr class="input-price">
                                                    <td>
                                                        Giá 1 người
                                                    </td>
                                                    <td>
                                                        <input type="text" placeholder="Giá bán 1 phòng" class="form-control" id="price-person" name="price_person">
                                                    </td>
                                                    <td>
                                                        <i>Giá phòng khi khách chỉ ở 1 người, nhập bằng 0 khi không có</i>
                                                    </td>
                                                </tr>
                                                <tr class="input-price">
                                                    <td>
                                                        Giá phòng
                                                    </td>
                                                    <td>
                                                        <input type="text" placeholder="Giá bán" class="form-control" id="price-day" name="price_room">
                                                    </td>
                                                    <td>
                                                        <i>Giá phòng khi khách ở tối đa số lượng người phòng cho phép</i>
                                                    </td>
                                                </tr>
                                            </tbody>
                                      </table>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="modal-footer">
                                    <button data-dismiss="modal" class="btn btn-default pull-left" type="button">Close</button>
                                    <button class="btn btn-success" type="submit">Cập nhật</button>
                                    <input type="hidden" name="hotel_id" value="0" class="hotel-id" />
                                    <input type="hidden" name="room_id" value="0" class="room-id" />
                                    <input type="hidden" name="rate_id" value="0" class="rate-id" />
                                    <input type="hidden" id="markup-ota" name="commission_ota" value="15" class="form-control">
                                    <input type="hidden" id="markup-ta" name="markup_ta" value="16" class="form-control">
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js-footer')
    <script src="{{ asset('assets/mytour/js/mytour.input_currency.js') }}"></script>
    <script type="text/javascript">
        //Date range picker
        $('.date-range').daterangepicker({
            format: 'DD/MM/YYYY'
        });

        function getPriceByDate(time_start, time_finish)
        {
            var _url = "{{ route('get-price-ajax') }}";
            $.ajax({
                type: "POST",
                url: _url,
                data: {time_start: time_start, time_finish: time_finish, _token: '{{ csrf_token() }}', hot_id: {{ $hotelID }} },
                success: function(data) {
                    $(".box-body").html(data);
                }
            });
        }

        function updateAllotment(obj, rID, time, hID, hPms)
        {
            var _url = "{{ route('update-allotment-of-day') }}";
            var value = $(obj).attr('data-value');

            $.ajax({
                type: "POST",
                url: _url,
                data: {rID: rID, time: time, _token: '{{ csrf_token() }}', value: value, 'hID': hID, 'hPms': hPms},
                success: function(data) {
                    if (data == 2) {
                        $(obj).attr('data-value', 0);
                        $(obj).removeClass('text-green').addClass('text-red');
                        $(obj).html('<i class="fa fa-lock fa-2x"></i>Khóa phòng');
                        $("#input-price .allotment_lock_" + rID + time).addClass("bg-light-pink");
                    } else if(data == 1) {
                        if (!hPms) {
                            $(obj).attr('data-value', 1);
                            $(obj).removeClass('text-red').addClass('text-green');
                            $(obj).html('<i class="fa fa-unlock fa-2x"></i>Mở phòng');
                            $("#input-price .allotment_lock_" + rID + time).removeClass("bg-light-pink");
                        } else {
                            $(obj).attr('data-value', 1);
                            $(obj).removeClass('text-red').addClass('text-green');
                            $(obj).html('<i>&nbsp;</i>');
                            $("#input-price .allotment_lock_" + rID + time).removeClass("bg-light-pink");
                        }
                    } else {
                        alert('Lỗi, bạn vui lòng thử lại!');
                    }
                }
            });
        }

        function add_option_modal(obj, id)
        {
            var hotel_id    = $(obj).attr('data-hotel');
            var rom_id      = $(obj).attr('data-room');
            var rate_id     = $(obj).attr('data-rate-id');
            var person_type = $(obj).attr('data-person-type');

            $('#' + id).find('input.hotel-id').val(hotel_id);
            $('#' + id).find('input.room-id').val(rom_id);
            $('#' + id).find('input.rate-id').val(rate_id);
            $('#' + id).find('input.person-type').val(person_type);

            $('#' + id).hide('div.alert');
        }

        $("#allotment-range-time").submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: data,
                success: function (response) {
                    response = $.parseJSON(response);
                    $("#allotment-range-time .msg-alert-allotment").html(response.msg);
                    if (response.load_page) {
                        setTimeout(function(){document.location.reload();}, 1000);
                    }
                }
            })
        });

        $("#price-contract-range-time").submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: data,
                success: function (response) {
                    response = $.parseJSON(response);
                    $("#price-contract-range-time .msg-alert-price-contract").html(response.msg);
                    if (response.load_page) {
                        setTimeout(function(){document.location.reload();}, 1000);
                    }
                }
            })
        });

        $("#price-ota-range-time").submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: data,
                success: function (response) {
                    response = $.parseJSON(response);
                    $("#price-ota-range-time .msg-alert-price").html(response.msg);
                    if (response.load_page) {
                        setTimeout(function(){document.location.reload();}, 1000);
                    }
                }
            })
        });

        function hiddenPrice(obj, rrpID)
        {
            var type  = $(obj).data('type-hidden');
            var value = $(obj).data('hidden-price');

            $.ajax({
                type: 'POST',
                url: "{{ route('hidden-price-ajax') }}",
                data: {rrp_id: rrpID, value: value, 'type': type, _token: '{{ csrf_token() }}'},
                success: function (response) {
                    if (response) {
                        $(obj).attr('data-hidden-price', Math.abs(value - 1));
                        if (type == 'hidden-price') {
                            if (value == 0) {
                                $("#rap_price_email_" + rrpID).prop('checked', false);
                                $("#rap_price_email_" + rrpID).data('hidden-price', value);
                            }
                        } else {
                            if (value == 0) {
                                $("#rap_hidden_price_" + rrpID).prop('checked', false);
                                $("#rap_hidden_price_" + rrpID).data('hidden-price', value);
                            }
                        }
                    } else {
                        alert('Lỗi, bạn hãy thử lại!');
                    }
                }
            });
        }

        function checkTaxFee(obj, hotId)
        {
            var valueCheck = $(obj).data('hot-tax-fee');

            $.ajax({
                type: 'POST',
                url: "{{ route('check-tax-fee-ajax') }}",
                data: {hotId: hotId, valueCheck: valueCheck, _token: '{{ csrf_token() }}'},
                success: function (response) {
                    if (response) {
                        $(obj).attr('data-hot-tax-fee', Math.abs(valueCheck - 1));
                    } else {
                        alert('Có lỗi trong quá trình cập nhật, bạn hãy thử lại!');
                    }
                }
            });
        }

        $(document).ready(function () {
            $('.collapse')
            .on('shown', function() {
                $(this)
                .parent()
                .find(".fa-angle-down")
                .removeClass("fa-angle-down")
                .addClass("glyphicon-minus");
            })
            .on('hide', function() {
                $(this)
                .parent()
                .find(".fa-angle-down")
                .removeClass("fa-angle-down")
                .addClass("glyphicon-plus");
            });
        });

    </script>
@stop