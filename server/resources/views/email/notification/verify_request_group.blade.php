<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8" />
	</head>
	<body>

	<p><strong>A group has requested verification:</strong></p>
    <p><strong>Name:</strong>  {{ $group['name'] }}</p>
    <p><strong>Display Name:</strong> {{ $group['display_name'] }}</p>
    <p><strong>Address 1:</strong> {{ $group['address1'] }}</p>
    <p><strong>Address 2:</strong> {{ $group['address2'] }}</p>
    <p><strong>City:</strong> {{ $group['city'] }}</p>
    <p><strong>State:</strong> {{ $group['state'] }}</p>
    <p><strong>Postal Code:</strong> {{ $group['postal_code'] }}</p>
    <p><strong>Phone Number:</strong> {{ $group['phone_number'] }}</p>

    <a href="{{ URL::to('administrative-dashboard/verify-group') }}">View Request</a>

	<p>&ndash; The OpenGov Foundation Team</p>
	</body>
</html>
