<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8" />
    </head>
    <body>
        <p>{{ $subcomment->user->getDisplayName() }} commented on your <a href="{{ $parent->getLink() }}">{{ $parent['type'] }}</a>:</p>
        <blockquote>{{ $subcomment->text }}</blockquote>
        Jump back over to <a href="{{ $subcomment->getLink() }}">the conversation</a> to respond!

        <p>&ndash; The OpenGov Foundation Team</p>
    </body>
</html>
