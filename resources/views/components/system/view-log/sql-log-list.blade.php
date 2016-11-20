<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<ul>
	@foreach($files as $fileName => $url)
	<li>
		<a href="{{ $url }}">{{ $fileName }}</a> - <a href="{{ route('sql-log-clear', ['filename' => $fileName]) }}">Clear</a>
	</li>
	@endforeach
</ul>
</body>
</html>