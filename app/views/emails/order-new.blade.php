<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div>
			<p>Hello, {{ $name or null }}</p>
			
			<p>You have made a new order #{{ $orders_id or null }}.</p> 
			<p>Price:</p>
			<p>Content:</p>
			<p>Status:</p>
			<p>Comment:</p>
			
			<p>We will contact you soon.</p>
			
			<p>You can check your order <a href="{{ $link or null }}">here</a> with your secret code: {{ $secret or null }}</p>
			
			<p>You can login to your newly created account with your email and password: <strong>{{ $password or null }}</strong></p>
			
			<p>Thank you!</p>
		</div>
	</body>
</html>