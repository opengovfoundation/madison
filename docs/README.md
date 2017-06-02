# Welcome to the Madison Documentation!

## Contents:

* [Introduction](#introduction)
* [Development Notes](#development-notes)
* [Configuration](#configuration)
* [Administration](#administration)
* [Localization](#localization)
* [Contributing](#contributing)

## Introduction:

Madison is an open-source platform built by [The OpenGov
Foundation](http://opengovfoundation.org) that facilitates collaboration on
policy between citizens, government, and stakeholders.  Madison allows citizens
to interact directly with legislation before it becomes law, by commenting,
asking questions, and offering improvements directly on legislation under
consideration.

**For Citizens:**
This is your chance to tell policymakers how you really feel. Comment, ask
questions, or suggest changes directly on legislation before it becomes law.
These are your laws; it's time for you to have your say.

**For Authors:**
There's never been an easier way to get substantive feedback from both
colleagues and citizens. Offer and receive input in real time from fellow
policymakers, issue experts, and the citizens you represent. With Madison, your
job has never been easier.

**For Developers:**
Check out the rest of the documentation and read through the [Contributing
Guidelines](#contributing). Pull requests welcome!

## Development Notes

Madison is a fairly standard Laravel application. For information on local
development, see [`docs/develop.md`](/docs/develop.md).

You can also [view the release notes](/docs/RELEASE_NOTES.md).

## Configuration

Madison is build on top of [Laravel v.5.4](http://laravel.com/docs/5.4) and uses
many of configuration tools that [Laravel
provides](https://laravel.com/docs/5.4#configuration).

### .env file

Regardless of your configuration method, you'll need to setup a `.env`
file for the application in the document root directory.  We have included a
sample file, `.env.example`.  You can find more details about [configuring
Laravel in the Laravel
documentation](https://laravel.com/docs/5.1#environment-configuration), but here
are the values you will need to set:

#### General Settings

* `APP_ENV`: For your live site, you probably want to set this to `production`,
  which will prevent migrations, etc from happening and making the site go
  offline temporarily.
* `APP_KEY`: This is used to encrypt private info, such as sessions. Set it to
  a random string with `php artisan key:generate`.
* `APP_DEBUG`: This should be `false` on your live site, hiding any system
  errors and debugging info from your users.
* `APP_LOG_LEVEL`: The minimum level of log messages you want showing up in log
  files. Keep at `debug` for local dev and something higher for production.
* `APP_NAME`: This is the name that the server will show for pages when they're
  shared on social media and other places.
* `APP_URL`: This is the domain that your application will be served from.
* `ADMIN_EMAIL`: The email address of the initial site administrator user.
* `ADMIN_PASSWORD`: The default password of the initial site administrator user.

#### Storage settings

* `DB_CONNECTION`: The database engine to use, probably `MySQL` or `Postgres`.
  You can use `SQLite` but we don't recommend it or support it.
* `DB_HOST`: The hostname of your database.
* `DB_POST`: The port to connect to the database through.
* `DB_DATABASE`: The database name.
* `DB_USERNAME`: The database username.
* `DB_PASSWORD`: The database password.

#### Drivers

* `BROADCAST_DRIVER`: The driver for broadcast style notifications in Laravel.
* `CACHE_DRIVER`: The driver for the site cache. We recommend `redis`.
* `SESSION_DRIVER`: The driver for the session storage.  Right now, we recommend `file`.
* `QUEUE_DRIVER`: The driver to use for queue workers. We recommend `database`,
  or `sync` for local development.

#### Redis

* `REDIS_HOST`: The host for your redis instance.
* `REDIS_PASSWORD`: The password for your redis instance.
* `REDIS_POST`: The port for your redis instance.

#### Mail settings

We strongly recommend you signup for a mailer service such as Sendgrid, Mailgun,
Mandrill, Amazon SES, etc.  [More info about mailers and
settings.](https://laravel.com/docs/5.2/mail)

* `MAIL_DRIVER`: The driver to use (per the available Laravel options).
* `MAIL_HOST`: The host of the mail service.
* `MAIL_PORT`: The port to send mail to.
* `MAIL_USERNAME`: The username of your mailer account.
* `MAIL_PASSWORD`: The password of your mailer account.
* `MAIL_ENCRYPTION`: The encryption level to use for email.
* `MAIL_FROM_ADDRESS`: The email address to show as the sender.
* `MAIL_FROM_NAME`: The name to show as the sender.

#### Rollbar

* `ROLLBAR_SERVER_TOKEN`: Token for server reported errors, provided by Rollbar.
* `ROLLBAR_CLIENT_TOKEN`: Token for client reported errors, provided by Rollbar.
* `ROLLBAR_LEVEL`: Level of reporting to Rollbar.

#### Pusher

**Note:** Madison is not utilizing broadcast notifications at this time.

* `PUSHER_APP_ID`: The pusher application ID.
* `PUSHER_APP_KEY`: Key provided by pusher for this application.
* `PUSHER_APP_SECRET`: Secret provided by pusher.

#### Google Analytics

* `GA`: Your tracking code (`UA-00000000-01`) for a Google Analytics account for tracking page views.

## Administration

Administering Madison is pretty simple at this stage of the project.  Any
Madison administrator will have extra options in their account dropdown in the
top-right corner of the application under `Admin` after logging in.

These options include:

* Site Settings
  * Date Format
  * Time Format
  * Google Analytics Key
* Custom Pages
  * Manage custom content pages throughout the site
* Featured Documents
  * Manage which documents show as "featured" on the home page
* Manage Users
  * Master list of all users in the system, searchable
  * Ability to edit users, and make / remove admin privileges
* Manage Sponsors
  * List of all sponsors in the system, searchable
  * Can approve or deny sponsor approval requests

## Localization

* We're using Laravel's i18n system, language files are located in
  `resources/lang/`.
  * We also have sponsor onboarding localized in lang-code specific folders in
    `resources/views/sponsor/onboarding/`.
* Make a new directory for the language code you wish to support in
  `resources/lang/`, then copy over each file from the `en/` folder.
* Open each PHP file in the new directory and translate the values of each array
  element into the language you're interested in. Note, only translate the
  *values*, do not change the keys of the elements as that will break the
  lookup of the translation.

We encourage you to create new locale files and submit them back to the project!
We hope to support many languages in the future.

### Annotations

* Madison uses [AnnotatorJS](http://annotatorjs.org/) as its Annotation engine.
  This library is only loaded on document pages.
* **Annotator loads and saves all annotations from / to the api**
* We have created an [Annotator
  Plugin](http://docs.annotatorjs.org/en/v1.2.x/hacking/plugin-development.html)
  to add Madison-specific functionality to Annotator.

## Contributing

Please include a descriptive message (and if possible an issue link) with each
pull request. We will try to merge PRs as quickly as possible.

### Code Style

* Make sure to use the included `.editorconfig` file in your editor.
  Instructions can be found [on the EditorConfig site](http://editorconfig.org/)
  on integrating it with your code editor.
* All PHP code should be styled using `PSR-2` guidelines.  Please make sure to
  run [`php-cs-fixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer) before
  committing code.
