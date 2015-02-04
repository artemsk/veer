<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div>
			<p>Hello, {{ $name or null }}</p>
			
			<p>We have news for your order #{{ $orders_id or null }}. Its status changed to:</p> 
			<p><strong>{{ array_get($status, 'name') }}</strong></p>
			@if(array_has($status, 'comments'))<p>{{ array_get($status, 'comments') }}</p>
			@endif<p>You can check your order <a href="{{ $link or null }}">here</a>.</p>
		</div>
	</body>
</html>