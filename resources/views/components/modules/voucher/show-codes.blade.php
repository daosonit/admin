@extends('layouts.master')

@section('js')
<script type="text/javascript">
	$('a.btn-remove').click(function(event){
		return confirm('Bạn muốn xóa mã này ?');
	});
</script>
@stop

@section('content')
<div class="box">
{!!  Form::open(['method' => 'GET', 'name' => 'search']) !!}
<div class="box-header">
    <h3 class="box-title"></h3>
    <div class="box-tools">
        <div class="input-group input-group-sm" style="width: 150px;">
            <input type="text" name="code" value="{{ Request::get('code') }}" class="form-control pull-right" placeholder="Search">
            <div class="input-group-btn">
                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </div>
</div>
{!!  Form::close() !!}
<!-- /.box-header -->
<div class="box-body table-responsive no-padding">
    <table class="table table-hover">
        <tr>
        	<th>STT</th>
            <th>ID</th>
            <th>Code</th>
            <th>Action</th>
        </tr>
        @if($voucherCodes->count())
            @foreach($voucherCodes as $voucherCode)
                <tr>
                	<td>{{ $voucherCode->stt }}</td>
                    <td>{{ $voucherCode->id }}</td>
                    <td>{{ $voucherCode->code or 'No Code' }}</td>
                    <td>
                        <a class="btn btn-danger btn-remove" title="Edit  voucher" href="{{ route('voucher-codes-delete', $voucherCode->id) }}"><i class="glyphicon glyphicon-trash"></i></a> 
                    </td>
                </tr>
            @endforeach
        @endif
    </table>
</div>
{!! $voucherCodes->appends(Request::query())->render() !!}
<!-- /.box-body -->
</div>
@stop