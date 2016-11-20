@extends('layouts.master')

@section('title')
	Add new voucher
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
    	<div class="box box-info">
            {!! Form::open(['name' => 'voucher', 'method' => 'POST', 'class' => '']) !!}
            <div class="box-header">
                <h3 class="box-title">Create a new voucher</h3>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label>Tên chương trình</label>
                    {!! Form::text('name', '', ['class' => 'form-control']) !!}
                </div>
                <!-- radio -->
                <div class="form-group">
                 <label class="text-primary">Kiểu mã</label>
                    <div class="input-group my-colorpicker2">
                        <label>
                            {!! Form::radio('type', App\Mytour\Classes\Voucher::SINGLE_CODE, true, ['class' => 'minimal']) !!} &nbsp;&nbsp;Single Code
                        </label>
                        <br>
                        <label>
                            {!! Form::radio('type', App\Mytour\Classes\Voucher::MULTI_CODE, false, ['class' => 'minimal']) !!} &nbsp;&nbsp;Multi Code
                        </label>
                    </div>
                </div>
                <!-- /.form group -->
                <div class="form-group">
                    <label class="text-primary">Hình thức khuyến mãi</label>
                    <div class="input-group my-colorpicker2">
                        <label>
                            {!! Form::radio('discount_type', App\Mytour\Classes\Voucher::DISCOUNT_MONEY_TYPE, true, ['class' => 'minimal']) !!} &nbsp;&nbsp; Giảm trừ bằng tiền
                        </label>
                        <br>
                        <label>
                            {!! Form::radio('discount_type', App\Mytour\Classes\Voucher::DISCOUNT_GIFT_TYPE, false, ['class' => 'minimal']) !!} &nbsp;&nbsp; Quà tặng
                        </label>
                        <br>
                        <label>
                            {!! Form::radio('discount_type', App\Mytour\Classes\Voucher::DISCOUNT_VPOINT_TYPE, false, ['class' => 'minimal']) !!} &nbsp;&nbsp; Tặng vpoint
                        </label>
                    </div>
                </div>
               
                <!-- /.form group -->
                <div class="form-group">
                    <label class="text-primary">Thời gian áp dụng</label>
                    <div class="input-group">
                        <div class="input-group">
                            <label>
                                <input type="checkbox" class="minimal" checked>&nbsp;&nbsp; Ngày đặt
                                {!! Form::checkbox('') !!}
                            </label>
                            <br>
                            <label>
                                <input type="checkbox" class="minimal">&nbsp;&nbsp; Thời gian ở
                            </label>
                        </div>
                    </div>
                    <!-- /.input group -->
                </div>
                <!-- /.form group -->
                 <!-- /.form group -->
                <div class="form-group">
                    <label class="text-primary">Chính sách hủy</label>
                    <div class="input-group my-colorpicker2">
                        <label>
                            {!! Form::radio('discount_type', App\Mytour\Classes\Voucher::DISCOUNT_MONEY_TYPE, true, ['class' => 'minimal']) !!} &nbsp;&nbsp; Không hoàn hủy
                        </label>
                        <br>
                        <label>
                            {!! Form::radio('discount_type', App\Mytour\Classes\Voucher::DISCOUNT_GIFT_TYPE, false, ['class' => 'minimal']) !!} &nbsp;&nbsp; Theo chính sách hủy của khách sạn
                        </label>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
            {!! Form::close() !!}
        </div>
    </div>
    <div class="col-md-6">
    
    </div>
</div>
@endsection