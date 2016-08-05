<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8" />
	</head>
	<body>

		<p>A user - {{ $user['fname'] }} {{ $user['lname'] }} ({{ $user['email'] }}) has requested independent sponsor status.</p>

        <p><a href="{{ URL::to('administrative-dashboard/verify-independent') }}">View Request</a></p>

		<p>&ndash; The OpenGov Foundation Team</p>
	</body>
</html>
