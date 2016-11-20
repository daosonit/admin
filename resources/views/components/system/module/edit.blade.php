@extends('layouts.master')

@section('title')
	Edit {{ $module->mod_name }} Module
@endsection

@section('content')
<div class="row">
    <!-- left column -->
    <div class="col-md-6">
        <!-- general form elements -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <h3 class="box-title">Edit <span class="label label-success">{{ $module->mod_name }}</span> module</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            {!! Form::model($module, array('route' => array('module-edit', $module->mod_id))) !!}
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="form-group">
                    {!! Form::label('mod_name', 'Module name', array('class' => 'text-primary')); !!}
                    {!! Form::text('mod_name', $module->mod_name,['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('mod_group_id', 'Module group', array('class' => 'text-primary')); !!}
                    {!! Form::select('mod_group_id', $listGroup, $module->mod_group_id,['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('mod_listname', 'Danh sách menu', array('class' => 'text-primary')); !!}
                    {!! Form::text('mod_listname', $module->mod_listname,['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('mod_listroute', 'Danh sách route', array('class' => 'text-primary')); !!}
                    {!! Form::text('mod_listroute', $module->mod_listroute,['class' => 'form-control']) !!}
                </div>

                 <div class="form-group">
                    {!! Form::label('mod_listfile', 'Danh sách file', array('class' => 'text-primary')); !!}
                    {!! Form::text('mod_listfile', $module->mod_listfile,['class' => 'form-control']) !!}
                </div>



                 <div class="form-group">
                    {!! Form::label('mod_name', 'Thứ tự', array('class' => 'text-primary')); !!}
                    {!! Form::text('mod_order', $module->mod_order,['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! Form::submit('Lưu',['class' => 'form-control bnt btn-primary']) !!}
                </div>

                
            {!! Form::close() !!}
        </div>
        <!-- /.box -->
    </div>   
</div>
<!-- /.row -->
@endsection