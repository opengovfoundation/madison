<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>Madison Password Reset</h2>

        <div>
            To reset your password, complete this form: {{ link_to('password/reset/' . $token) }}.
        </div>
    </body>
</html>
