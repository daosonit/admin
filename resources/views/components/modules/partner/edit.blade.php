@extends('layouts.master')
@section('title')
    Sửa partner.
@endsection

@section('content')
    <div class="col-md-6">
        @if (count($errors) > 0)
            @error($errors->all())
        @endif
        @if (session('status'))
            @status(session('status'))
        @endif

        {!! Form::model($partner,$form['model']) !!}
        <div class="box-body">
            @select($form['pn_type'])
            @text($form['pn_name'])
            @text($form['pn_link'])
            @files($form['pn_logo'])
            @textarea($form['pn_info'])
            @checkbox($form['pn_active'])
            @submit($form['submit'])
        </div>
        {!! Form::close() !!}
    </div>
@endsection