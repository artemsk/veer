<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div>
			<p>Hello, {{ $name or null }}</p>
			
			<p>You had been mentioned in comment @if(!empty($link))<a href="{{ $link }}"><strong>{{ $place or '?' }}</strong></a>@endif:</p> 
			<p><strong>{{ $sender }}</strong>: {{ $txt }}</p>
		</div>
	</body>
</html>