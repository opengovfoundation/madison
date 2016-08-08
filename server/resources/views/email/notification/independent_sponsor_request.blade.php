{{-- Receives $user[] --}}
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8" />
	</head>
	<body>

	<p><strong>A user has requested independent sponsor status:</strong></p>
    <p><strong>Name:</strong>  {{ $user['fname'] }} {{ $user['lname'] }}</p>
    <p><strong>Address 1:</strong> {{ $user['address1'] }}</p>
    <p><strong>Address 2:</strong> {{ $user['address2'] }}</p>
    <p><strong>City:</strong> {{ $user['city'] }}</p>
    <p><strong>State:</strong> {{ $user['state'] }}</p>
    <p><strong>Postal Code:</strong> {{ $user['postal_code'] }}</p>
    <p><strong>Phone Number:</strong> {{ $user['phone'] }}</p>

    <a href="{{ URL::to('administrative-dashboard/verify-sponsors') }}">View Request</a>

	<p>&ndash; The OpenGov Foundation Team</p>
	</body>
</html>
