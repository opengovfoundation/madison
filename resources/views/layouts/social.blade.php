<!DOCTYPE html>
<html>
    <head>
        <title><?php echo config('app.name') . ' - ' . $title; ?></title>
        <meta property="og:title" content="<?php echo config('app.name') . ' - ' .  $title; ?>" />
        <meta property="og:description" content="<?php echo strip_tags($description); ?>" />
        <meta property="og:image" content="<?php echo $image; ?>" />
        <!-- etc. -->
    </head>
    <body>
        <p><?php echo $description; ?></p>
        <img src="<?php echo $image; ?>">
    </body>
</html>
