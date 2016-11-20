@extends('layouts.master')
@section('title')
    Profile - {{ $user->name }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3">
            <!-- Profile Image -->
            <div class="box box-primary">
                <div class="box-body box-profile">

                    <img class="profile-user-img img-responsive img-circle" src="{{ $user->getAvatar() }}"
                         alt="User profile picture">

                    <h3 class="profile-username text-center">{{ $user->name }}</h3>

                    <p class="text-muted text-center">Software Engineer</p>
                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>Department</b> <a class="pull-right">{{ $user->department->name or 'unknow' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Following</b> <a class="pull-right">543</a>
                        </li>
                        <li class="list-group-item">
                            <b>Friends</b> <a class="pull-right">13,287</a>
                        </li>
                    </ul>
                    <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>

        <div class="col-md-9">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="{{session('tab') == 2?'':'active'}}">
                        <a href="#info_account" data-toggle="tab">Thông tin tài khoản</a>
                    </li>
                    <li class="{{session('tab') == 2?'active':''}}">
                        <a href="#change_password" data-toggle="tab">Đổi mật khẩu</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="{{session('tab') == 2?'':'active'}} tab-pane" id="info_account">

                        @if (session('status_info'))
                            <div class="alert flash-message text-center alert-info alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert"
                                        aria-hidden="true">&times;</button>
                                {!! session('status_info') !!}
                            </div>
                        @endif

                        {!! Form::model($user,['method'=>'PUT','route'=>['profile.edit'],'files'=>true, 'class'=>'form-horizontal'])  !!}

                        <div class="form-group">
                            {!! Form::label('name','Tên',array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('name',$user->name, array('class'=>'form-control', 'placeholder'=>'Name')) !!}
                                {!! $errors->first('name', '<div class="text-danger">:message</div>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('email','Email',array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('email',$user->email, array('class'=>'form-control', 'placeholder'=>'Email','readonly'=>'readonly')) !!}
                                {!! $errors->first('email', '<div class="text-danger">:message</div>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('avatar','Avatar',array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::file('avatar',array('class'=>'form-control')) !!}
                                <p class="help-block">(Dung lượng tối đa 500 Kb có tỉ lệ 4x3)</p>
                                {!! $errors->first('avatar', '<div class="text-danger">:message</div>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('phone','SĐT',array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('phone',$user->phone, array('class'=>'form-control', 'placeholder'=>'Số điện thoại')) !!}
                                {!! $errors->first('phone', '<div class="text-danger">:message</div>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('address','Địa chỉ',array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('address',$user->address, array('class'=>'form-control', 'placeholder'=>'Địa chỉ')) !!}
                                {!! $errors->first('address', '<div class="text-danger">:message</div>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('identity_card','CMND',array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('identity_card',$user->identity_card, array('class'=>'form-control', 'placeholder'=>'Số CMND','readonly'=>'readonly')) !!}
                                {!! $errors->first('identity_card', '<div class="text-danger">:message</div>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                {!! Form::submit('Cập nhật', array('class'=>'btn btn-primary')) !!}
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>

                    <div class="{{session('tab') == 2?'active':''}} tab-pane" id="change_password">

                        @if (session('status'))
                            <div class="alert flash-message text-center alert-info alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert"
                                        aria-hidden="true">&times;</button>
                                {!! session('status') !!}
                            </div>
                        @endif

                        {!! Form::open(array('route' => 'profile.change_password','class'=>'form-horizontal')) !!}

                        <div class="form-group">
                            {!! Form::label('password_old', 'Mật khẩu cũ', array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::password('password_old',array('id'=>'password_old','class'=>'form-control','style'=>'width:50%')) !!}
                                {!! $errors->first('password_old', '<div class="text-danger">:message</div>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('password', 'Mật khẩu mới', array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::password('password',array('id'=>'password','class'=>'form-control','style'=>'width:50%')) !!}
                                {!! $errors->first('password', '<div class="text-danger">:message</div>') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('password_confirmation', 'Xác nhận mật khẩu', array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::password('password_confirmation',array('id'=>'password_confirmation','class'=>'form-control','style'=>'width:50%')) !!}
                                {!! $errors->first('password_confirmation', '<div class="text-danger">:message</div>') !!}

                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                {!! Form::submit('Thay đổi', array('class'=>'btn btn-danger')) !!}
                            </div>
                        </div>
                        {!! Form::close() !!}

                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>

    </div>
    <!-- /.row -->
@endsection