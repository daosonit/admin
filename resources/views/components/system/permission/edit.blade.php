@extends('layouts.master')

@section('title')
	Create a new Permission
@endsection

@section('content')
<div class="row">
    <!-- left column -->
    <div class="col-md-6">
        <!-- general form elements -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <h3 class="box-title">Create a new Permission</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form action="" method="POST" >
            	<input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="box-body">
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
                        <label for="name">Permission Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ $permission->name }}" placeholder="Enter name">
                    </div>
                    <div class="form-group">
                        <label for="display_name">Display Name</label>
                        <input type="text" name="display_name" class="form-control" id="display_name" value="{{ $permission->display_name }}" placeholder="Display name">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" name="description" class="form-control" id="description" value="{{ $permission->description }}" placeholder="Description">
                    </div>

                    
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
        <!-- /.box -->
    </div>   
</div>
<!-- /.row -->
@endsection