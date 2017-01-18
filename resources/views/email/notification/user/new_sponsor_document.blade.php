{{--
Expects:
  $sponsor (Sponsor)
  $doc (Doc)
  $user_id (int)
--}}

<!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta charset="utf-8" />
  </head>
  <body>
    <p>Just a heads up,</p>

    <p>Your sponsor {{ $sponsor['name'] }} has just posted a new document!</p>

    <p>Jump back over to Madison to read '<a href="{{ URL::to('documents/' . $doc['slug']) }}">{{ $doc['title'] }}</a>'</p>

    <p>If you want to turn off notifications for new sponsor documents, please visit your <a href="{{ URL::to('/user/edit/' . $user_id . '/notifications') }}">Notification Settings Page</a></p>

    <p>&ndash; The OpenGov Foundation Team</p>
  </body>
</html>
