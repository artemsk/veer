<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div>
			<p>Hello, {{ $name or null }}</p>
			
			<p>You have a message from <strong>{{ $sender }}</strong>:</p> 
			<p>{{ $txt }}</p>
			<small>@if(!empty($link))<a href="{{ $link }}">{{ $place or '?' }}</a>@endif</small>
		</div>
	</body>
</html>