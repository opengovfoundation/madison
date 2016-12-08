<!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta charset="utf-8" />
  </head>
  <body>
    <p>Congrats,</p>

    <p>{{ $subcomment['user']['fname'] }} {{ substr($subcomment['user']['lname'], 0, 1) }} commented on your <a href="{{ $activity['link'] }}">{{ $activity['type'] }}</a>:</p>

    &quot; {{ $subcomment['text'] }} &quot;

    Jump back over to <a href="{{ $subcomment['link'] }}">the conversation</a> to respond!

    <p>&ndash; The OpenGov Foundation Team</p>
  </body>
</html>
