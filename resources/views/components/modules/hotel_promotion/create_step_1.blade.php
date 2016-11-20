@extends('layouts.master')

@section('title')
	Tạo mới Khuyến mãi
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <!-- /.box-header -->
            <!-- form start -->
            {!! Form::open(['route' => 'hotel-promo-create-post-step-1', 'method' => 'POST']) !!}
                <div class="box-body">
                    @include('layouts.includes.show-error-validate')
                    <div class="form-group col-sm-3">
                        <label>Chọn kiểu Khuyến mãi (OTA / TA) <i class="text-red">*</i></label>
                        <select class="form-control" name="promo_type">
                            <option value="0">TA</option>
                            <option value="1">OTA</option>
                        </select>
                    </div>
                    <input type="hidden" name="hotel_id" value="{{ $hotelID }}">
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <button class="btn btn-info pull-left" type="submit">Tiếp theo <i class="fa fa-arrow-circle-right "></i></button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@stop