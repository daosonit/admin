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
                {!! Form::text('date_range_book', date("d/m/Y", $infoPromo->proh_time_book_start) . " - " . date("d/m/Y", $infoPromo->proh_time_book_finish), ['class' => 'form-control pull-right date-range']) !!}
            </div>
            <?
                $day_apply_every = $infoPromo->proh_day_apply;
            ?>
            <div class="radio">
                <label>
                    <input type="radio" {!! $day_apply_every->isEmpty() ? 'checked=""' : '' !!} value="1" name="day_apply_every">
                    Áp dụng mọi thời điểm thuộc khoảng thời gian đặt phòng
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" {!! !$day_apply_every->isEmpty() ? 'checked=""' : '' !!} value="0" name="day_apply_every">
                    Vào ngày giờ cụ thể
                </label>
            </div>
            <div class="bg-gray collapse {!! !$day_apply_every->isEmpty() ? 'in' : '' !!}" id="collapseOne" {{ !$day_apply_every->isEmpty() ? 'aria-expanded="true"' : '' }} >
                <div class="box-body">
                    <div class="form-group checkbox-week">
                        <p class="no-margin">Chỉ áp dụng cho các ngày cụ thể trong tuần</p>
                        @for($i = 1; $i <= 6; $i++)
                            <div class="pull-left">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('proh_day_apply[' . $i . ']', $i, (!$day_apply_every->isEmpty() && isset($day_apply_every['day_apply'][$i])) ? $day_apply_every['day_apply'][$i] : 0) !!}
                                        Thứ {{ $i + 1 }}
                                    </label>
                                </div>
                            </div>
                        @endfor
                        <div class="pull-left">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('proh_day_apply[7]', 7, (!$day_apply_every->isEmpty() && isset($day_apply_every['day_apply'][7])) ? $day_apply_every['day_apply'][7] : 0) !!}
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
                            {!! Form::text('time_start_apply', isset($day_apply_every['time_start']) ? $day_apply_every['time_start'] : "", ['class' => 'form-control pull-right time-picker']) !!}
                        </div>
                        <div class="input-group col-sm-4">
                            <label class="col-sm-1">Đến</label>
                            <div class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                            {!! Form::text('time_finish_apply', isset($day_apply_every['time_finish']) ? $day_apply_every['time_finish'] : "", ['class' => 'form-control pull-right time-picker']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
