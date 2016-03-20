<!DOCTYPE html>
<html>
    <head>
        <title>{{ config('app.name') . ' - ' . $title }}</title>
        <meta property="og:title" content="{{ config('app.name') . ' - ' .  htmlentities($title) }}" />
        <meta property="og:description" content="{{ htmlentities(strip_tags($description)) }}" />
        <meta property="og:image" content="{{ url($image) }}" />
        <!-- etc. -->
    </head>
    <body>
        <p>{{ $description }}</p>
        <img src="{{ url($image) }}">
    </body>
</html>
