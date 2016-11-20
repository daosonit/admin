@extends('layouts.master')


@section('js')
<script type="text/javascript">
$(document).ready(function () {
    $("#textarea").tokenInput("{{ route('hotel-suggest') }}");
});
</script>
@stop

@section('content')

<input type="text" id="textarea" class="example" rows="1"></input>

@stop