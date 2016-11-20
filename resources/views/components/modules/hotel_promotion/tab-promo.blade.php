<div class="box box-info">
    @if (old('proh_type') == $proh_type)
        <!-- show alert error validate -->
        @include('layouts.includes.show-error-validate')
    @endif

    <div class="box-body">
        <div class="form-group">
            <label for="">Tiêu đề Khuyến mãi <i class="text-red">*</i></label>
            {!! Form::text($prefix . 'proh_title', null, ['class' => 'form-control', 'id' => 'proh_title']) !!}
            {!! Form::hidden('proh_promo_type', $typePromo) !!}
            {!! Form::hidden('hotel_id', $hotelID) !!}
        </div>
    </div>
    <div class="box-header with-border">
        <h3 class="box-title">Thời gian ở</h3>
        <i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Chọn ngày nhận phòng được áp dụng khuyến mãi</i>
    </div>
    <div class="box-body">
        <div class="form-group">
            <label for="">Khoảng thời gian nhận phòng</label>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                {!! Form::text($prefix . 'date_range_checkin', null, ['class' => 'form-control pull-right date-range']) !!}
            </div>
        </div>

        <div class="form-group col-sm-6">
            <div class="bg-light-pink padding-10 day-not-apply">
                <label for="">Ngày không áp dụng</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input class="form-control date-picker" name="proh_day_deny[]" type="text"/>
                    <span class="input-group-btn btn-add-input">
                        <button class="btn btn-info btn-flat btn-add-more" onclick="btn_add_more(this)" type="button"><i class="fa fa-plus-square fa-2x"></i></button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.box-body -->
@if ($proh_type == App\Models\Components\Promotion::TYPE_PROMO_CUSTOM)
    @include('components.modules.hotel_promotion.tab-promo-custom')
    <input type="hidden" value="{!! $proh_type !!}" name="proh_type">
@elseif ($proh_type == App\Models\Components\Promotion::TYPE_PROMO_EARLY)
    @include('components.modules.hotel_promotion.tab-promo-early')
    <input type="hidden" value="{!! $proh_type !!}" name="proh_type">
@elseif ($proh_type == App\Models\Components\Promotion::TYPE_PROMO_LASTMINUTE)
    @include('components.modules.hotel_promotion.tab-promo-last-minutes')
    <input type="hidden" value="{!! $proh_type !!}" name="proh_type">
@endif

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Chính sách hủy</h3>
    </div>
    <div class="box-body">
        <div class="form-group col-sm-12">
            <div class="radio">
                <label>
                    {!! Form::radio('proh_cancel_policy', 1, true) !!}
                    Giữ nguyên chính sách hủy của đơn giá áp dụng
                </label>
            </div>
            <div class="radio">
                <label>
                    {!! Form::radio('proh_cancel_policy', 0) !!}
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
                        {!! Form::text($prefix . 'proh_min_night', 1, ['class' => 'form-control']) !!}
                    </td>
                    <td>đêm</td>
                </tr>
                <tr>
                    <td>Ở tối đa: </td>
                    <td>
                        {!! Form::text($prefix . 'proh_max_night', 30, ['class' => 'form-control']) !!}
                    </td>
                    <td>đêm</td>
                </tr>
            </table>
        </div>
    </div>
</div>
    <!-- /.box-body -->

<div class="box-footer">
    <a href="{{ route('hotel-promo-create-step-1', $hotelID) }}">
        <button class="btn btn-info pull-left" type="button"><i class="fa fa-arrow-circle-left "></i> Quay lại</button>
    </a>
    {!! Form::submit('Cập nhật', array('class' => 'btn btn-info pull-right')) !!}
</div>
