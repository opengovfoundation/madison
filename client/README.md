# Madison Public

This is the public facing interface for the Madison project where public
engagement through discussion and annotation on draft legislation happens.

## Setup

This project requires that [Madison core](0) be set up and running in the
`madison-3` branch on your system to work. Madison core is the API side of the
system. Below is an example apache configuration to make it all work.

```
<VirtualHost *:80>
    ServerName mymadison.local
    ServerAlias mymadison.local

    DocumentRoot "/path/to/madison-public/app"
    Alias "/api" "/path/to/madison/public"

    ErrorLog "/var/log/apache2/mymadison.local-error_log"
    CustomLog "/var/log/apache2/mymadison.local-access_log" common

    <Directory "/Users/sethetter/code/madison-public/app">
        Options indexes followsymlinks
        DirectoryIndex index.html
        FallbackResource /index.html
        AllowOverride all
        Allow from all
        Require all granted
    </Directory>

    <Directory "/path/to/madison/public">
        Options indexes followsymlinks
        DirectoryIndex index.php
        AllowOverride all
        Allow from all
        Require all granted
    </Directory>
</VirtualHost>
```

## Customization

There are a few different mechanisms for customizing Madison to your liking.

### Language & i18n

The majority of the language in the system is defined in the `app/locales/`
folder within relevant JSON files, defined for each supported language. If you
create a file that matches the necessary language name in
`app/locales/custom/` this will overwrite what is set as default in the base
language files.

### Style

Any stylesheets placed within the `app/sass/custom/` folder will be processed
during the build and can overwrite any existing styles. You can also change sass
variables here. Reference the sass config files in `app/sass/config/` to see
what variables can be changed.


[0]: https://github.com/opengovfoundation/madison
