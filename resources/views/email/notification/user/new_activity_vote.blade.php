{{--
Expects:
  $intro (string)
  $user (User)
  $vote_type (string)
  $activity (Comment || Annotation)
--}}

<!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta charset="utf-8" />
  </head>
  <body>
    <p>{{ $intro }},</p>

    <p>{{ $user['fname'] }} {{ substr($user['lname'], 0, 1) }} {{ $vote_type }}d your <a href="{{ $activity['link'] }}">{{ $activity['type'] }}!</a></p>

    <p>Jump back over to <a href="{{ $activity['link'] }}">the conversation</a> to respond!</p>

    <p>If you want to turn off vote notifications, please visit your <a href="{{ URL::to('/user/edit/' . $activity['user']['id'] . '/notifications') }}">Notification Settings Page</a></p>

    <p>&ndash; The OpenGov Foundation Team</p>
  </body>
</html>
