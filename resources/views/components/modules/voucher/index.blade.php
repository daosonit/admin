@extends('layouts.master')

@section('title')
	Danh sách voucher 
@endsection


@section('js')
<script type="text/javascript">
    $('button.view-voucher').click(function(event){
        var voucherId = $(this).attr('data-voucher-id');
        var url = '{{ route("voucher-codes") }}';
            url += "/"+voucherId;

        $('#show-voucher-codes').attr('src', url);
    }); 
</script>
@stop


@section('content')
	<!-- /.row -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Danh sách Voucher</h3>
                </div>
                
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    {!!  Form::open(['method' => 'GET', 'name' => 'search']) !!}
                        <div class="box-body" >
                            <div class="box-tools pull-left">
                                <div class="input-group input-group-sm">
                                    {!! Form::text('id', Request::get('id'), ['class' => 'form-control', 'placeholder' => 'ID']) !!}
                                </div>
                            </div>
                            <div class="box-tools pull-left">
                                <div class="input-group input-group-sm">
                                    {!! Form::text('code', Request::get('code'), ['class' => 'form-control', 'placeholder' => 'Code']) !!}
                                </div>
                            </div>

                            <div class="box-tools">
                                <div class="input-group input-group-sm" style="width: 150px;">
                                    {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Tên Voucher']) !!}
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {!!  Form::close() !!}
                    <table class="table table-hover">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                        @if($voucherData->count())
	                        @foreach($voucherData->all() as $voucher)
		                        <tr>
		                            <td>{{ $voucher->id }}</td>
		                            <td>{{ $voucher->name }}</td>
                                    <td><span class="label label-success">{{ strtoupper($voucher->type) }}</span></td>
                                    @if($voucher->type == App\Mytour\Classes\VoucherSystem::SINGLE_CODE)
		                            <td><span class="label label-success">{{ $voucher->voucherCodes->first()->code or 'No Code' }}</span></td>
                                    @else 
                                    <td>
                                        <button type="button" data-voucher-id="{{ $voucher->id }}" class="view-voucher btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
                                          View Code
                                        </button>
                                    </td>
                                    @endif
		                            <td>{!! $voucher->description !!}</td>
		                            <td>
			                            <a title="Edit {{ $voucher->name }} voucher" href="{{ route('voucher-edit', $voucher->id) }}"><i class="text-large glyphicon glyphicon-edit"></i></a> | 
		                            	<a title="Remove {{ $voucher->name }} voucher" href="{{ route('voucher-delete', $voucher->id) }}"><i class="text-large text-red glyphicon glyphicon-remove"></i></a>
		                            	
		                            </td>
		                        </tr>
	                        @endforeach
	                    @endif
                    </table>
                </div>
                
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
            {!! $voucherData->appends(Request::query())->render() !!}
        </div>
    </div>

<!-- Button trigger modal -->
    <!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Danh sách voucher code</h4>
      </div>
      <div class="modal-body">
        <iframe style="height: 550px;" id="show-voucher-codes" src=""></iframe>
      </div>
    </div>
  </div>
</div>
@endsection

