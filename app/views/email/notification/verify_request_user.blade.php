<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8" />
	</head>
	<body>

		<p>A user - {{ $user['fname'] }} {{ $user['lname'] }} ({{ $user['email'] }}) has requested profile verification.</p>

    <a href="{{ URL::to('dashboard/verifications') }}">View Request</a>
		
		<p>&ndash; The OpenGov Foundation Team</p>
	</body>
</html>


