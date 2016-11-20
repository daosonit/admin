@extends('layouts.master')

@section('title')
    Edit Voucher
@endsection
@section('js')
<script>
  $(function () {
    //Datemask dd/mm/yyyy
    $(".datemask").inputmask("dd-mm-yyyy", {
        "placeholder": "dd-mm-yyyy"
    });

  });
</script>

<script type="text/javascript">
    
    $('input.dc_type').on('ifChecked', function(event){
        $(this).parents('div.input-group').find('div.dc_type_group').addClass('hidden');
        $(this).parents('label.dc_type_label').next().removeClass('hidden');
    });

    $('input.allow_setting').on('ifChanged', function(event){
        var checked = !$(this).parent('[class*="icheckbox"]').hasClass("checked");
        if(checked) {
            $(this).parents('label.allow_setting_table').next().removeClass('hidden');
        } else {
            $(this).parents('label.allow_setting_table').next().addClass('hidden');
        }

    });

    $(function () {
        $('input[name="time_checkin_apply"]:checked, input[name="discount_type"]:checked ,input[name="advance_setting"]:checked, input[name="type"]:checked').parents('label.dc_type_label').next().removeClass('hidden');
        
    });
    $(document).ready(function () {
        $("#hotel_allow").tokenInput("{{ route('hotel-suggest') }}", {
            prePopulate: {!! $hotels !!},
            preventDuplicates: true,
            minChars: 2,
            hintText: 'Nhập tên khách sạn.',
            noResultsText: 'Không có khách sạn nào được tìm thấy!',
            searchingText: 'Đang tìm kiếm...'
        });
        $("#hotel_city_allow").tokenInput("{{ route('city-suggest') }}", {
            prePopulate: {!! $cities !!},
            preventDuplicates: true,
            minChars: 2,
            hintText: 'Nhập tên tỉnh thành của khách sạn.',
            noResultsText: 'Không có tỉnh thành nào được tìm thấy!',
            searchingText: 'Đang tìm kiếm...'
        });


        
    });
</script>
@endsection

@section('css')
    <style type="text/css">
    table.table label {
        padding: 5px!important;
    }
    </style>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            {!! Form::open(['name' => 'voucher', 'method' => 'POST', 'class' => '', 'files' => true]) !!}
            <div class="box-header">
                <h3 class="box-title">Edit {{ $voucher->name }} voucher</h3>
            </div>
            <div class="box-body">
                @include('show-errors')
                <div class="form-group">
                    <label class="text-primary">Tên chương trình</label>
                    {!! Form::text('name', $voucher->name, ['class' => 'form-control']) !!}
                    <br>
                    <label class="text-primary">Mô tả</label>
                    {!! Form::textarea('description', $voucher->description, ['class' => 'form-control textarea']) !!}
                </div>
                <!-- radio -->
                <hr>
                <div class="form-group">
                 <label class="text-primary">Kiểu mã</label>
                    <div class="input-group">
                        <label class="dc_type_label">
                            {!! Form::radio('type', App\Mytour\Classes\VoucherSystem::SINGLE_CODE, $voucher->type == App\Mytour\Classes\VoucherSystem::SINGLE_CODE ? true : false, ['class' => 'dc_type minimal']) !!} &nbsp;&nbsp;Single Code
                        </label>
                        <div class="form-group hidden dc_type_group">
                            <div class="form-group">
                            <table class="table">
                                <tr>
                                    <td><label>Nhập mã:</label></td>
                                    <td>
                                        {!! Form::text('code', $voucher->voucherCodes->first() ? $voucher->voucherCodes->first()->code : '', ['placeholder' => 'Nhập mã code', 'class' => 'form-control']) !!}
                                    </td>
                                </tr>
                            </table>
                            </div>
                        </div>
                        <br>
                        <label class="dc_type_label">
                            {!! Form::radio('type', App\Mytour\Classes\VoucherSystem::MULTI_CODE, $voucher->type == App\Mytour\Classes\VoucherSystem::MULTI_CODE ? true : false, ['class' => 'dc_type minimal']) !!} &nbsp;&nbsp;Multi Code
                        </label>
                        <div class="form-group hidden dc_type_group">
                             <table class="table">
                                <tr>
                                    <td><label>Nhập file:</label></td>
                                    <td>
                                        {!! Form::file('code_list', ['class' => 'form-control']); !!}
                                    </td>
                                </tr>
                            </table>
                            
                        </div>
                    </div>
                </div>
                <hr>
                <!-- /.form group -->
                <div class="form-group">
                    <label class="text-primary">Hình thức khuyến mãi</label>
                    <div class="input-group ">
                        <label class="dc_type_label">
                            {!! Form::radio('discount_type', App\Mytour\Classes\VoucherSystem::DISCOUNT_MONEY_TYPE, $voucher->discount_type == App\Mytour\Classes\VoucherSystem::DISCOUNT_MONEY_TYPE ? true : false, ['class' => 'minimal dc_type']) !!} &nbsp;&nbsp; Giảm trừ bằng tiền
                        </label>
                        <div class="hidden form-group dc_type_group">
                            <table class="table">
                                <tr>
                                    <td><label>Số tiền:</label></td>
                                    <td>{!! Form::input('number', 'money', $voucher->voucherDiscountInfo ? $voucher->voucherDiscountInfo->money : '', ['class' => 'form-control']) !!}</td>
                                    <td>{!! Form::select('money_type', [ 1 => '% giá trị đơn phòng'  , 3 => 'VND'], 1, ['class' => 'form-control']) !!}</td>
                                </tr>
                                <tr>
                                    <td><label>Số tiền tối đa:</label></td>
                                    <td colspan="2">{!! Form::input('number', 'money_max', $voucher->voucherDiscountInfo ? $voucher->voucherDiscountInfo->money_max : '', ['class' => 'form-control']) !!}</td><td></td>
                                </tr>
                            </table>
                            
                        </div>
                        <br>
                        <label class="dc_type_label">
                            {!! Form::radio('discount_type', App\Mytour\Classes\VoucherSystem::DISCOUNT_GIFT_TYPE, $voucher->discount_type == App\Mytour\Classes\VoucherSystem::DISCOUNT_GIFT_TYPE ? true : false, ['class' => 'minimal dc_type']) !!} &nbsp;&nbsp; Quà tặng
                        </label>
                        <div class="hidden form-group dc_type_group">
                            <table>
                                <tr>
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </td>
                                    <td><label>Thông tin quà tặng:</label></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>{!! Form::textarea('gift_info', $voucher->voucherDiscountInfo ? $voucher->voucherDiscountInfo->gift_info : '', ['class' => 'form-control textarea']) !!}</td>
                                </tr>
                            </table>
                            
                        </div>
                        <br>
                        <label class="dc_type_label">
                            {!! Form::radio('discount_type', App\Mytour\Classes\VoucherSystem::DISCOUNT_VPOINT_TYPE, $voucher->discount_type == App\Mytour\Classes\VoucherSystem::DISCOUNT_VPOINT_TYPE ? true : false, ['class' => 'minimal dc_type']) !!} &nbsp;&nbsp; Tặng vpoint
                        </label>
                         <div class="hidden form-group dc_type_group">
                            <label class="text-primary">Thông tin vpoint:</label>
                             <table class="table">
                                <tr>
                                    <td><label class="">Giá trị vpoint:</label></td>
                                    <td>{!! Form::text('vpoint', $voucher->voucherDiscountInfo ? $voucher->voucherDiscountInfo->vpoint : '', ['class' => 'form-control']) !!}</td>
                                    <td>{!! Form::select('vpoint_type', [ 1 => '% giá trị đơn phòng'  , 2 => 'Vpoint'], $voucher->voucherDiscountInfo ? $voucher->voucherDiscountInfo->vpoint_type : 1, ['class' => 'form-control']) !!}</td>
                                </tr>
                                <tr>
                                    <td><label>Số vpoint tối đa</label></td>
                                    <td colspan="2">{!! Form::text('vpoint_max', $voucher->voucherDiscountInfo ? $voucher->voucherDiscountInfo->vpoint_max : '', ['class' => 'form-control']) !!}</td><td></td>
                                </tr>
                                 <tr>
                                    <td><label>Hạn sử dụng</label></td>
                                    <td colspan="2">{!! Form::text('vpoint_expire', $voucher->voucherDiscountInfo ? $voucher->voucherDiscountInfo->vpoint_expire : '', ['class' => 'form-control']) !!}</td><td></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
               <hr>
                <!-- /.form group -->
                <div class="form-group">
                    <label class="text-primary">Thời gian áp dụng</label>
                    <div class="input-group">
                        <div class="form-group">
                            <label>Thời gian đặt từ ngày:</label>
                            {!! Form::input('date', 'timebook_start', $voucher->timebook_start->toDateString(), ['class' => 'form-control', "data-inputmask" => "'alias': 'dd-mm-yyyy'", 'data-mask' => '']) !!}
                            <br>
                            <label>Đến ngày:</label>
                            {!! Form::input('date', 'timebook_finish', $voucher->timebook_finish->toDateString(), ['class' => 'form-control', "data-inputmask" => "'alias': 'dd-mm-yyyy'", 'data-mask' => '']) !!}
                            <br>
                           
                        </div>
                        <br>
                        <div class="form-group">
                            <label class="allow_setting_table dc_type_label">
                                {!! Form::checkbox('time_checkin_apply', 1, $voucher->time_checkin_apply, ['class' => 'allow_setting minimal']) !!} &nbsp;&nbsp; Cho phép thiết lập "Áp dụng thời gian checkin"
                            </label>
                            <div class="form_setting hidden">
                               <label>Từ ngày</label>
                                {!! Form::input('date', 'checkin_start', $voucher->checkin_start->toDateString(), ['class' => 'form-control', "data-inputmask" => "'alias': 'dd-mm-yyyy'", 'data-mask' => '']) !!}
                                <br>
                                <label>Đến ngày</label>
                                {!! Form::input('date', 'checkin_finish', $voucher->checkin_finish->toDateString(), ['class' => 'form-control', "data-inputmask" => "'alias': 'dd-mm-yyyy'", 'data-mask' => '']) !!}
                                <br>
                            </div>
                        </div>
                    </div>
                    <!-- /.input group -->
                </div>
                <!-- /.form group -->
                <hr>
                 <!-- /.form group -->
                <div class="form-group">
                    <label class="text-primary">Chính sách hủy</label>
                    <div class="input-group">
                        <label>
                            {!! Form::radio('cancellation_policy', App\Mytour\Classes\VoucherSystem::DEFAULT_CANC_PLC, $voucher->cancellation_policy == App\Mytour\Classes\VoucherSystem::DEFAULT_CANC_PLC ? true : false, ['class' => 'minimal']) !!} &nbsp;&nbsp; Mặc định
                        </label>
                        <br>
                        <label>
                            {!! Form::radio('cancellation_policy', App\Mytour\Classes\VoucherSystem::VOUCHER_CANC_PLC, $voucher->cancellation_policy == App\Mytour\Classes\VoucherSystem::VOUCHER_CANC_PLC ? true : false, ['class' => 'minimal']) !!} &nbsp;&nbsp; Không hoàn hủy
                        </label>
                        <br>
                        <label>
                            {!! Form::radio('cancellation_policy', App\Mytour\Classes\VoucherSystem::HOTEL_CANC_PLC, $voucher->cancellation_policy == App\Mytour\Classes\VoucherSystem::HOTEL_CANC_PLC ? true : false, ['class' => 'minimal']) !!} &nbsp;&nbsp; Theo chính sách hủy của khách sạn
                        </label>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <label class="text-primary">Tùy chọn cài đặt nâng cao</label>
                    <div class="input-group">
                        <label class="allow_setting_table dc_type_label">
                            {!! Form::checkbox('advance_setting', 1, $voucher->advance_setting, ['class' => 'allow_setting minimal']) !!} &nbsp;&nbsp; Cho phép thiết lập các chính sách áp dụng nâng cao
                        </label>
                        <div class="form_setting hidden">
                                                    <!-- /.box -->
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Điều kiện khách sạn</h3>
                            </div>
                            <div class="box-body">
                                <!-- Color Picker -->
                                <div class="form-group">
                                    <label>Thuộc tỉnh thành: </label>
                                    {!! Form::text('hotel_city_allow', '', ['id' => 'hotel_city_allow', 'class' => 'form-control']) !!}
                                </div>
                                <!-- /.form group -->
                                <!-- Color Picker -->
                                <div class="form-group">
                                    <label>Hạng sao:</label>
                                    <div class="input-group">
                                       <label>
                                           {!! Form::checkbox('hotel_star_rate_apply[1]', 1,$voucher->voucherAdvanceSetting ? in_array(1, $voucher->voucherAdvanceSetting->hotel_star_rate_apply->toArray()) : false, ['class' => 'minimal']) !!} &nbsp; 1 sao &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                       </label>
                                       <label>
                                           {!! Form::checkbox('hotel_star_rate_apply[2]', 2,$voucher->voucherAdvanceSetting ? in_array(2, $voucher->voucherAdvanceSetting->hotel_star_rate_apply->toArray()) : false, ['class' => 'minimal']) !!} &nbsp; 2 sao &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                       </label>
                                       <label>
                                           {!! Form::checkbox('hotel_star_rate_apply[3]', 3,$voucher->voucherAdvanceSetting ? in_array(3, $voucher->voucherAdvanceSetting->hotel_star_rate_apply->toArray()) : false, ['class' => 'minimal']) !!} &nbsp; 3 sao &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                       </label>
                                       <label>
                                           {!! Form::checkbox('hotel_star_rate_apply[4]', 4,$voucher->voucherAdvanceSetting ? in_array(4, $voucher->voucherAdvanceSetting->hotel_star_rate_apply->toArray()) : false, ['class' => 'minimal']) !!} &nbsp; 4 sao &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                       </label>
                                       <label>
                                           {!! Form::checkbox('hotel_star_rate_apply[5]', 5,$voucher->voucherAdvanceSetting ? in_array(5, $voucher->voucherAdvanceSetting->hotel_star_rate_apply->toArray()) : false, ['class' => 'minimal']) !!} &nbsp; 5 sao &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                       </label>
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <!-- /.form group -->
                                 <div class="form-group">
                                     <label>Chỉ các khách sạn thuộc danh sách: </label>
                                    {!! Form::text('hotel_allow', '', ['id' => 'hotel_allow', 'class' => 'form-control']) !!}
                                 </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <br>
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Điều kiện đơn phòng</h3>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                     <label>Giá trị tối thiểu: </label>
                                    {!! Form::input('number', 'booking_money_min', $voucher->voucherAdvanceSetting ? $voucher->voucherAdvanceSetting->booking_money_min :  '', ['class' => 'form-control']) !!}
                                    <br>
                                     <label>Giá trị tối đa: </label>
                                    {!! Form::input('number', 'booking_money_max', $voucher->voucherAdvanceSetting ? $voucher->voucherAdvanceSetting->booking_money_max :  '', ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Điều kiện khách hàng:</h3>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                     <div class="input-group">
                                         <label>
                                           {!! Form::checkbox('customer_logged_in', 1, $voucher->voucherAdvanceSetting ? $voucher->voucherAdvanceSetting->customer_logged_in :  false, ['class' => 'minimal']) !!} &nbsp; Chỉ áp dụng cho khách hàng đăng nhập.
                                       </label>
                                     </div>
                                 </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    {!! Form::submit('Lưu', ['name' => 'save', 'class' => 'btn btn-primary']) !!}
                </div>
            </div>
            <!-- /.box-body -->
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection