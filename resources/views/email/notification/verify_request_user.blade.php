<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8" />
    </head>
    <body>

        <p>A user - {{ $user['display_name'] }} ({{ $user['email'] }}) has requested profile verification.</p>

        <p><a href="{{ URL::to('administrative-dashboard/verify-account') }}">View Request</a></p>

        <p>&ndash; The OpenGov Foundation Team</p>
    </body>
</html>
