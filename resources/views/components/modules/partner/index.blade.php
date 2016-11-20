@extends('layouts.master')
@section('title')
    Danh sách partner.
@endsection
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Danh sách partner.</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    {!! Form::open($form['open']) !!}
                    <div class="box-tools pull-left">
                        <div class="input-group input-group-sm">
                            {!! Form::select('pn_type',$array_type , Request::get('pn_type'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="box-tools pull-left">
                        <div class="input-group input-group-sm">
                            {!! Form::text('pn_name', Request::get('pn_name'), ['class' => 'form-control', 'placeholder' => 'Tên đối tác']) !!}
                        </div>
                    </div>

                    <div class="box-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="box-body">

                    @if (session('status'))
                        <div class="alert flash-message text-center alert-info alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true">&times;</button>
                            {!! session('status') !!}
                        </div>
                    @endif

                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>STT</th>
                            <th>Nhóm đối tác</th>
                            <th>Tên đối tác</th>
                            <th>URL</th>
                            <th>Logo</th>
                            <th>Sửa</th>
                            <th>Xóa</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($all_partner as $key =>$value)
                            <tr>
                                <td>{{$value->pn_id}}</td>
                                <td>{{isset($array_type[$value->pn_type])?$array_type[$value->pn_type]:''}}</td>

                                <td><a href="{{$value->pn_link}}">{{$value->pn_name}}</a></td>
                                <td>{{$value->pn_link}}</td>
                                <td width="125">
                                    <img src="{{$value->getSmallLogo()}}" class="img-thumbnail" alt="Ảnh đại diện" width="120" height="90">
                                </td>

                                <td width="50">
                                    @update(route('modules.partners.edit', $value->pn_id))
                                </td>
                                <td width="50">
                                    @delete(route('modules.partners.destroy',$value->pn_id))
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>

    <div class="row">
        <div class="col-sm-5">
            <div class="dataTables_info" id="example1_info" role="status" aria-live="polite">
                Tất cả {!! $all_partner->total() !!}
            </div>
        </div>
        <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers">
                {!! $all_partner->render() !!}
            </div>
        </div>
    </div>
@endsection