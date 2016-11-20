@extends('layouts.master')

@section('title')
    Danh sách template email
    @endsection

    @section('content')
            <!-- /.row -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box">

                <div class="box-header">
                    <h3 class="box-title">Danh sách template email</h3> &nbsp;&nbsp;
                    <b><a class="btn btn-info" data-toggle="modal" href="#addNew">Add New</a></b>
                    <br>

                    @if($errors->count() > 0)
                        <div class="alert alert-danger fade in">
                            <a class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                            <strong>Thất bại!</strong>&nbsp;
                            {!! $errors->first('file_html', '<span>:message</span>') !!}
                            {!! $errors->first('file_picture', '<span>:message</span>') !!}
                        </div>
                    @endif

                    @if(session('modal') == 2)
                        <div class="alert alert-success fade in" style="margin-top:18px;">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                            <strong>Thành công!</strong>
                        </div>
                    @endif
                </div>

                @if($file_email->count() > 0)
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tr>
                            <th>No</th>
                            <th>Picture</th>
                            <th>Html</th>
                            <th>Time</th>
                            <th class="text-center">Action</th>
                        </tr>
                            @foreach ($file_email as $values)
                                <tr>

                                    <td>{!! $no !!}</td>

                                    <td>
                                        @if($values->file_picture != '')
                                            <a style="cursor: pointer" target="_blank" title="URL Image"
                                               href="{!! $values->urlImage() !!}">{!! $values->urlImage() !!}</a>
                                        @endif
                                    </td>

                                    <td>
                                        @if($values->file_html != '')
                                            <a style="cursor: pointer" target="_blank" title="URL File Html"
                                               href="{!! $values->urlFile() !!}">{!! $values->urlFile() !!}</a>
                                        @endif
                                    </td>

                                    <td>{!! date('d/m/Y',$values->time_create) !!}</td>

                                    <td class="text-center">
                                        <a data-toggle="modal" href="#email_{{$values->id}}">
                                            <i style="cursor: pointer" class="glyphicon glyphicon-trash"></i>
                                        </a>
                                        @include('layouts.includes.modal-delete', ['href' => 'email_'.$values->id, 'url' => route('modules.manage-email.destroy',$values->id)])
                                    </td>

                                    <? $no++ ?>
                                </tr>
                            @endforeach

                        <tr>
                            <th>No</th>
                            <th>Picture</th>
                            <th>Html</th>
                            <th>Time</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </table>
                    @else
                        <small>Không có bản ghi!</small>
                    @endif
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
            {!! $file_email->render()!!}
        </div>
    </div>
    <div id="addNew" class="modal text-left fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h1 class="modal-title">Upload file</h1>
                </div>
                {!! Form::open(['files' => true, 'route' => 'modules.manage-email.store']) !!}
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('file_picture', 'File picture:') !!}
                        {!! Form::file('file_picture')!!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('file_html', 'File html:') !!}
                        {!! Form::file('file_html') !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection