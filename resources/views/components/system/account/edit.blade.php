@extends('layouts.master')

@section('title')
	Update Account
@endsection

@section('content')
	<!-- Settings tab content -->
    <div class="tab-pane" id="control-sidebar-settings-tab">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Update Admin Account</h3>
                </div>
                <!-- form start -->
                {!! Form::model($admin, ['route' => ['admin-edit', $admin->id]]) !!}
                    <div class="box-body">
                        @include('show-errors')
                        @if(isset($alert))
                            <div class="alert alert-success">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <span>{{ $alert }}</span>
                             </div>
                        @endif
                        <div class="form-group">
                            {!! Form::label('email', 'Email') !!}
                            {!! Form::email('email', null, ['class' => 'form-control', 'readonly', 'id' => 'email', 'placeholder' => 'Enter Email']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('name', 'Name') !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'id' => 'name', 'placeholder' => 'Enter Name']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('department_id', 'Department') !!}
                            {!! Form::select('department_id', $departments, null, ['class' => 'form-control']) !!}
                        </div> 

                        <div class="form-group">
                            <label>Gender:&nbsp;&nbsp;&nbsp;
                            {!! Form::radio('gender', 0, null, ['class' => 'minimal']) !!} &nbsp;Male
                            </label>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <label for="rd_female">
                            {!! Form::radio('gender', 1, null, ['class' => 'minimal']) !!}&nbsp;Female
                            </label>
                        </div>

                        <div class="form-group">
                            {!! Form::label('phone', 'Phone') !!}
                            {!! Form::text('phone', null, ['class' => 'form-control', 'id' => 'phone', 'placeholder' => 'Enter Phone']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('address', 'Address') !!}
                            {!! Form::text('address', null, ['class' => 'form-control', 'id' => 'address', 'placeholder' => 'Enter Address']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('identity_card', 'Identity Card') !!}
                            {!! Form::text('identity_card', null, ['class' => 'form-control', 'id' => 'identity_card', 'placeholder' => 'Enter Identity Card']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::checkbox('active', 1, null, ['class' => 'form-control minimal']) !!}
                            {!! Form::label('active', 'Active') !!}
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection