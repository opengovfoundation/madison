<?php

if (! empty($greeting)) {
    echo $greeting, "\n\n";
} else {
    echo trans('messages.notifications.greeting.'.($level == 'error' ? 'error' : 'normal')), "\n\n";
}

if (! empty($introLines)) {
    echo implode("\n", $introLines), "\n\n";
}

if (isset($actionText)) {
    echo "{$actionText}: {$actionUrl}", "\n\n";
}

if (! empty($outroLines)) {
    echo implode("\n", $outroLines), "\n\n";
}

echo trans('messages.notifications.salutation', ['name' => config('app.name')]), "\n";
