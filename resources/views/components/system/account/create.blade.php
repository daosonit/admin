@extends('layouts.master')

@section('title')
	Create a new account
@endsection

@section('js')
<script type="text/javascript">
    $('button.btn-back').click(function(event){
        event.preventDefault();
        window.history.back();
    });
</script>
@stop


@section('content')
	<!-- Settings tab content -->
	<div class="tab-pane" id="control-sidebar-settings-tab">
		<div class="col-md-6">
	        <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Create Admin Account</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                {!! Form::open(['name' => 'create_admin', 'method' => 'POST']) !!}
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
                            {!! Form::email('email', '', ['class' => 'form-control', 'id' => 'email', 'placeholder' => 'Enter Email']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('password', 'Password') !!}
                            {!! Form::password('password', ['class' => 'form-control', 'id' => 'password', 'placeholder' => 'Password']) !!}
                        </div>

                        <div class="form-group">
                            <label for="repassword">Confirm Password</label>
                            {!! Form::password('password_confirmation', ['class' => 'form-control', 'id' => 'repassword', 'placeholder' => 'Repassword']) !!}
                        </div>

						<div class="form-group">
                            {!! Form::label('name', 'Name') !!}
                            {!! Form::text('name', '', ['class' => 'form-control', 'id' => 'name', 'placeholder' => 'Enter Name']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('department_id', 'Department') !!}
                            {!! Form::select('department_id', $departments, null, ['class' => 'form-control']) !!}
                        </div> 

                        <div class="form-group">
                            <label>Gender:&nbsp;&nbsp;&nbsp;
                            {!! Form::radio('gender', 0, true, ['class' => 'minimal']) !!} &nbsp;Male
                            </label>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <label for="rd_female">
                            {!! Form::radio('gender', 1, false, ['class' => 'minimal']) !!}&nbsp;Female
                            </label>
                        </div>

                        <div class="form-group">
                            {!! Form::label('phone', 'Phone') !!}
                            {!! Form::text('phone', '', ['class' => 'form-control', 'id' => 'phone', 'placeholder' => 'Enter Phone']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('address', 'Address') !!}
                            {!! Form::text('address', '', ['class' => 'form-control', 'id' => 'address', 'placeholder' => 'Enter Address']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('identity_card', 'Identity Card') !!}
                            {!! Form::text('identity_card', '', ['class' => 'form-control', 'id' => 'identity_card', 'placeholder' => 'Enter Identity Card']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::checkbox('active', 1, false, ['class' => 'form-control minimal']) !!}
                            {!! Form::label('active', 'Active') !!}
                        </div>

                        
                        <div class="form-group">
                            @if(Session::has('member'))
                            {!! Form::hidden('outside_email', Session::get('member')->use_email) !!}
                            @endif
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-back ">Back</button>
                        <button type="submit" class="btn btn-primary pull-right">Next</button>
                    </div>
                {!! Form::close() !!}
            </div>
	    </div>
	</div>
	<!-- /.tab-pane -->
@endsection