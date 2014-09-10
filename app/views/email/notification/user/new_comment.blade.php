<!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta charset="utf-8" />
  </head>
  <body>
    <p>Congrats,</p>

    <p>{{ $user->fname }} {{ substr($user->lname, 0, 1) }} commented on your [annotation || comment ](linked):</p>

    &quot; {{ $activity->text }} &quot;
    
    Jump back over to [the conversation](linked) to respond!

    <p>&ndash; The OpenGov Foundation Team</p>
  </body>
</html>


