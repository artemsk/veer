<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div>
			<p>Hello, {{ $name or null }}</p>
			
			<p>There is a bill for your order #{{ $orders_id or null }}.</p> 
			<p><strong>Bill #{{ $bills_id or null }}</strong></p>
			<p><a href="{{ $link or null }}">Please, check the link</a>.</p>
		</div>
	</body>
</html>