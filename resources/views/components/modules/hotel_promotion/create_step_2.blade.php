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
	Tạo mới khuyến mãi
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Custom Tabs (Pulled to the right) -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-left">
                @if (old('proh_type') == null || old('proh_type') == App\Models\Components\Promotion::TYPE_PROMO_CUSTOM)
                    <li class="active"><a data-toggle="tab" href="#hotel-promo-custom" aria-expanded="true">Khuyến mãi tùy chỉnh</a></li>
                @else
                    <li><a data-toggle="tab" href="#hotel-promo-custom" aria-expanded="false">Khuyến mãi tùy chỉnh</a></li>
                @endif

                @if (old('proh_type') == App\Models\Components\Promotion::TYPE_PROMO_EARLY)
                    <li class="active"><a data-toggle="tab" href="#hotel-promo-early" aria-expanded="true">Ưu đãi đặt sớm</a></li>
                @else
                    <li><a data-toggle="tab" href="#hotel-promo-early" aria-expanded="false">Ưu đãi đặt sớm</a></li>
                @endif

                @if (old('proh_type') == App\Models\Components\Promotion::TYPE_PROMO_LASTMINUTE)
                    <li class="active"><a data-toggle="tab" href="#hotel-promo-last-minutes" aria-expanded="true">Ưu đãi phút chót</a></li>
                @else
                    <li><a data-toggle="tab" href="#hotel-promo-last-minutes" aria-expanded="false">Ưu đãi phút chót</a></li>
                @endif

                <li><a data-toggle="tab" href="#hotel-promo-deal" aria-expanded="false">Khuyến mãi gói</a></li>
            </ul>
            <div class="clearfix"></div>
            <div class="tab-content">
                <!-- TAB KM tuy chinh -->
                <div id="hotel-promo-custom" class="tab-pane{!! (old('proh_type') == null || old('proh_type') == App\Models\Components\Promotion::TYPE_PROMO_CUSTOM) ? ' active' : '' !!}">
                    <!-- form start -->
                    {!! Form::open(['route' => 'hotel-promo-create-post-step-2', 'name' => 'rate-plan', 'method' => 'POST', 'id' => 'hotel-promo']) !!}
                        @include('components.modules.hotel_promotion.tab-promo', ['proh_type' => App\Models\Components\Promotion::TYPE_PROMO_CUSTOM, 'prefix' => 'custom_'])
                    {!! Form::close() !!}
                </div>
                <!-- END TAB KM tuy chinh -->

                <!-- TAB KM Uu dai dat som -->
                <div id="hotel-promo-early" class="tab-pane{!! old('proh_type') == App\Models\Components\Promotion::TYPE_PROMO_EARLY ? ' active' : '' !!}">
                    <!-- form start -->
                    {!! Form::open(['route' => 'hotel-promo-create-post-step-2', 'name' => 'rate-plan', 'method' => 'POST', 'id' => 'hotel-promo']) !!}
                        @include('components.modules.hotel_promotion.tab-promo', ['proh_type' => App\Models\Components\Promotion::TYPE_PROMO_EARLY, 'prefix' => 'early_'])
                    {!! Form::close() !!}
                </div>
                <!-- END TAB KM Uu dai dat som -->

                <!-- TAB KM Uu dai phut chot -->
                <div id="hotel-promo-last-minutes" class="tab-pane{!! old('proh_type') == App\Models\Components\Promotion::TYPE_PROMO_LASTMINUTE ? ' active' : '' !!}">
                    <!-- form start -->
                    {!! Form::open(['route' => 'hotel-promo-create-post-step-2', 'name' => 'rate-plan', 'method' => 'POST', 'id' => 'hotel-promo']) !!}
                        @include('components.modules.hotel_promotion.tab-promo', ['proh_type' => App\Models\Components\Promotion::TYPE_PROMO_LASTMINUTE, 'prefix' => 'last_'])
                    {!! Form::close() !!}
                </div>
                <!-- END TAB KM Uu dai phut chot -->

                <!-- TAB KM Goi -->
                <div id="hotel-promo-deal" class="tab-pane">
                    <!-- form start -->

                </div>
                <!-- END TAB KM Goi -->
            </div>
            <!-- /.tab-content -->
        </div>
        <!-- nav-tabs-custom -->
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

        function show_option_free_night(obj, prefix)
        {
            var value = $(obj).val();
            if (value == {!! App\Models\Components\Promotion::TYPE_DISCOUNT_FREE_NIGHT !!}) {
                $('.option-free-night-' + prefix).show();
                $('.option-percent-' + prefix).hide();
            } else {
                $('.option-percent-' + prefix).show();
                $('.option-free-night-' + prefix).hide();
            }
        }

        //Call plugin datepicker
        date_picker();
    </script>
@stop



