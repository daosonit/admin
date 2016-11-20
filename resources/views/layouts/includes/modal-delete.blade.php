<div id="{{$href}}" class="modal text-left fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h1 class="modal-title">Delete Data</h1>
            </div>
            <div class="modal-body">
                <p> Are you sure want to delete this data? </p>
            </div>

            <div class="modal-footer">
                {!! Form::open(['method' => 'DELETE', 'url' => $url])!!}
                {!! Form::hidden('page', Input::get('page',0)) !!}
                {!! Form::submit('Yes', array('class'=>'btn btn-primary')) !!}
                {!! Form::button('No', array('class'=>'btn btn-primary','data-dismiss'=>'modal')) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>