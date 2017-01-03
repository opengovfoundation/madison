<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8" />
    </head>
    <body>

    <p><strong>A sponsor has requested verification:</strong></p>
    <p><strong>Name:</strong>  {{ $sponsor['name'] }}</p>
    <p><strong>Display Name:</strong> {{ $sponsor['display_name'] }}</p>
    <p><strong>Address 1:</strong> {{ $sponsor['address1'] }}</p>
    <p><strong>Address 2:</strong> {{ $sponsor['address2'] }}</p>
    <p><strong>City:</strong> {{ $sponsor['city'] }}</p>
    <p><strong>State:</strong> {{ $sponsor['state'] }}</p>
    <p><strong>Postal Code:</strong> {{ $sponsor['postal_code'] }}</p>
    <p><strong>Phone Number:</strong> {{ $sponsor['phone_number'] }}</p>

    <a href="{{ URL::to('administrative-dashboard/verify-sponsor') }}">View Request</a>

    <p>&ndash; The OpenGov Foundation Team</p>
    </body>
</html>
