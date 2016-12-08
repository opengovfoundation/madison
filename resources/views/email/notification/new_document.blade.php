<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8" />
    </head>
    <body>

        <p>A new document has been created: </p>
    <p><strong>{{ link_to('docs/' . $doc['slug'], $doc['title']) }}</strong></p>

        <p>&ndash; The OpenGov Foundation Team</p>
    </body>
</html>
