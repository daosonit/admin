<!-- /.box-body -->
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
                        {!! Form::text('proh_min_day_before', $infoPromo->proh_min_day_before, ['class' => 'form-control']) !!}
                    </td>
                    <td>ngày</td>
                </tr>
            </table>
        </div>
    </div>
</div>
