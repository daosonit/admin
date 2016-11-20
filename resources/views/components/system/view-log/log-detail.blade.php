<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<div class="col-md-12">
	<a class="btn btn-primary" href="{{ route('log-clear', ['filename' => $fileName]) }}">Clear Log</a>
</div>
<div class="col-md-12">
	{!! nl2br($content) !!}	
</div>
</body>
</html>