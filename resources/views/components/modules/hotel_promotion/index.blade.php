@extends('layouts.master')

@section('title')
    Quản lý thông tin khuyến mãi
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
            <div class="box-header margin">
                <h3 class="box-title">Quản lý thông tin Khuyến mãi</h3>

                <div class="box-tools">
                    <div class="pull-right">
                        @if ($hotelPms)
                            <a class="btn btn-block btn-info" href="{!! route('hotel-promo-create-step-2', [$hotelID, 0]) !!}">Thêm Khuyến Mãi</a>
                        @else
                            <a class="btn btn-block btn-info" href="{!! route('hotel-promo-create-step-1', $hotelID) !!}">Thêm Khuyến Mãi</a>
                        @endif
                    </div>
                    <!-- <div style="width: 150px;" class="input-group input-group-sm">
                        <input type="text" placeholder="Search" class="form-control pull-right" name="table_search">

                        <div class="input-group-btn">
                            <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </div> -->
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
                <!-- show alert cap nhat thong tin -->
                @include('layouts.includes.show-alert')

                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Thời gian nhận phòng</th>
                            <th>Thời gian đặt phòng</th>
                            <th>Ngày không áp dụng</th>
                            <th>Kiểu khuyến mãi</th>
                            <th>Loại khuyến mãi</th>
                            <th class="text-center">&nbsp;</th>
                        </tr>
                        @if (!$dataPromo->isEmpty())
                            @foreach ($dataPromo as $infoPromo)
                                <tr>
                                    <td>{!! $infoPromo->proh_id !!}</td>
                                    <td>{!! $infoPromo->proh_title !!}</td>
                                    <td>{!! ($infoPromo->proh_time_start > 0 && $infoPromo->proh_time_finish > 0) ? date('d/m/Y', $infoPromo->proh_time_start) . ' - ' . date('d/m/Y', $infoPromo->proh_time_finish) : '' !!}</td>
                                    <td>{!! ($infoPromo->proh_time_book_start > 0 && $infoPromo->proh_time_book_finish > 0) ? date('d/m/Y', $infoPromo->proh_time_book_start) . ' - ' . date('d/m/Y', $infoPromo->proh_time_book_finish) : '' !!}</td>
                                    <td>

                                    </td>
                                    <td>
                                        @if ($infoPromo->proh_type == App\Models\Components\Promotion::TYPE_PROMO_EARLY)
                                            Ưu đãi đặt sớm
                                        @elseif ($infoPromo->proh_type == App\Models\Components\Promotion::TYPE_PROMO_LASTMINUTE)
                                            Ưu đãi phút chót
                                        @elseif ($infoPromo->proh_type == App\Models\Components\Promotion::TYPE_PROMO_CUSTOM)
                                            Khuyến mãi tùy chỉnh
                                        @endif
                                    </td>
                                    <td>{!! $infoPromo->proh_promo_type ? 'KM OTA' : 'KM TA' !!}</td>
                                    <td class="text-center">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="proh_active" value="{{ $infoPromo->proh_active }}"  {{ (!$hotelPms || !$infoPromo->proh_promo_type) ? '' : 'disabled' }} data-id="{{ $infoPromo->proh_id }}" {!! $infoPromo->proh_active ? 'checked' : '' !!}> Kích hoạt
                                            </label>
                                        </div>
                                        @if (!$hotelPms || !$infoPromo->proh_promo_type)
                                            <div class="btn-group-vertical">
                                                <a href="{!! route('edit-hotel-promo', $infoPromo->proh_id) !!}" class="btn btn-info" style="margin-bottom: 8px;"><i class="fa fa-edit fa-lg"></i> Sửa</a>
                                                <a href="javascript:void(0)" data-id="{{ $infoPromo->proh_id }}" class="btn btn-danger proh_delete"><i class="fa fa-trash fa-lg"></i> Xóa</a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                {!! $dataPromo->render() !!}
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
@stop

@section('js-footer')
    <script type="text/javascript">
        $("input[name='proh_active']").on('click', function (){
            var obj = $(this);
            var id  = obj.data('id');
            var val = obj.val();
            $.ajax({
                type: 'GET',
                url: "{{ route('active-hotel-promo') }}",
                data: {id: id, val: val},
                success: function (response) {
                    if (response) {
                        obj.val(Math.abs(val - 1));
                    } else {
                        alert("{!! ERROR_ALERT !!}");
                    }
                }
            })
        });

        $(".proh_delete").on('click', function (){
            var obj = $(this);
            var id  = obj.data('id');
            if (confirm("{!! DELETE_ALERT !!}")) {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('delete-hotel-promo') }}",
                    data: {id: id},
                    success: function (response) {
                        if (response) {
                            alert("{!! SUCCESS_ALERT !!}");
                            $(obj).parents('tr').remove();
                        } else {
                            alert("{!! ERROR_ALERT !!}");
                        }
                    }
                })
            }
        });
    </script>
@stop