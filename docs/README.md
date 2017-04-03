# Welcome to the Madison Documentation!

## Contents:

* [Introduction](#introduction)
* [Installation](#installation)
* [Administration](#administration)
* [Theming](#theming)
* [Architecture and Development notes](#architecture-and-development-notes)
* [Localization](#localization)
* [Contributing](#contributing)
* [Changelog](#changelog)

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

## Installation

Madison is build on top of [Laravel v.5.3](http://laravel.com/docs/5.3) and uses
many of configuration tools that Laravel provides ( specifically its [`.env`
files](https://laravel.com/docs/5.3#environment-configuration) )

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
* `APP_DEBUG`: This should be `false` on your live site, hiding any system
  errors and debugging info from your users.
* `APP_KEY`: This is used to encrypt private info, such as sessions.  Set it to
  a random string.
* `APP_NAME`: This is the name that the server will show for pages when they're
  shared on social media.
* `APP_URL`: This is the domain that your application will be served from.
* `COOKIE_DOMAIN`: The domain that cookies will be shared for.  [More info on
  cookie domains.](http://erik.io/blog/2014/03/04/definitive-guide-to-cookie-domains/)
* `ADMIN_EMAIL`: The email address of the site administrator.
* `ADMIN_PASSWORD`: The default password of the site administrator.

#### Storage settings

* `DB_CONNECTION`: The database engine to use, probably `MySQL` or `Postgres`.
  You can use `SQLite` but we don't recommend it or support it.
* `DB_HOST`: The hostname of your database.
* `DB_DATABASE`: The database name.
* `DB_USERNAME`: The database username.
* `DB_PASSWORD`: The database password.
* `CACHE_DRIVER`: The driver for the site cache. We recommend `file`.
* `SESSION_DRIVER`: The driver for the session storage.  Right now, we recommend `file`.
* `QUEUE_DRIVER`: The driver to use for queue workers. We recommend `database`.

#### Mail settings

We strongly recommend you signup for a mailer service such as Sendgrid, Mailgun,
Mandrill, Amazon SES, etc.  [More info about mailers and
settings.](https://laravel.com/docs/5.2/mail)

* `MAIL_DRIVER`: The driver to use (per the available Laravel options).
* `MAIL_HOST`: The host of the mail service.
* `MAIL_PORT`: The port to send mail to.
* `MAIL_USERNAME`: The username of your mailer account.
* `MAIL_PASSWORD`: The password of your mailer account.
* `MAIL_FROM_ADDRESS`: The email address to show as the sender.
* `MAIL_FROM_NAME`: The name to show as the sender.

#### Social Authentication

**Note:** Social login is [currently not
working](https://phabricator.opengovfoundation.org/T76).

We allow several services for OAuth for users to login to the system. You will
need to signup for accounts on each service you which to provide, and fill in
the API info as follows:

* `FB_CLIENT_ID`, `FB_CLIENT_SECRET`: Facebook credentials.
* `TW_CLIENT_ID`, `TW_CLIENT_SECRET`: Twitter credentials.
* `LI_CLIENT_ID`, `LI_CLIENT_SECRET`: LinkedIn credentials.

#### Plugin Features

* `USERVOICE`: We use the Uservoice app for feedback, you can drop in your user account with
* `GA`: Your tracking code (`UA-00000000-01`) for a Google Analytics account for tracking page views.

## Administration

Administering Madison is pretty simple at this stage of the project.  Any
Madison administrator will have extra options in their account dropdown in the
top-right corner of the application under `Administrative Dashboard` after
logging in.  These options include:

* `Edit Documents`
  * Administrators have access to all documents in Madison.  This option will
    display all documents with links to the document editor for each.
  * The option to create new documents is found at the bottom of this page.
  * When choosing the "publish state" of a document, the following rules apply:
    * *Published*: Publicly viewable and listed in the front page index
    * *Unpublished*: Only accessible by the sponsor of the document
    * *Private*: Not listed on the front page, but accessible by anyone who has
      the direct link
* `Verify Sponsors`
  * Sponsors can be used for different organizations, legislative offices, or
    individuals.  Users request to create a sponsor from their `Sponsor
    Management` page and must be approved by an admin.  Sponsors have the
    ability to post new documents on Madison.
* `Administrative Notification Settings`
  * Site admins may want to be notified when activity is happening on their
    installation.  This page allows them to subscribe to email notifications for
    events in Madison.
* `Site Settings`
  * This is where site Admins can change the `Featured Document` on Madison.
    This can also be changed in the in`Document Information` tab in each
    document editor, but gives Admins another option

## Theming

**Note: Madison's styles are built using [Compass](http://compass-style.org/)
which uses [SASS](http://sass-lang.com/).  You'll need to have a decent
knowledge of SASS and CSS to customize the theme currently.**

To customize the Madison design, simply add a new file in the
`public/sass/custom/` directory with your custom styles.  All files in this
directory will be pulled in automatically [_after_ the global variables are
defined](https://github.com/opengovfoundation/madison/blob/master/public/sass/style.scss#L25).
This will allow you to use or override the existing variables, as well as adding
custom styles.

The site also has a `.madison` class on the body of the page, so you can
override individual style rules by copying any existing rule and adding
`.madison` as the top level selector.  For instance, if you want to override the
`.navbar` background color, simply apply a new color to `.madison .navbar`
instead.

To see a live example, have a look at [DC's custom theme
file](https://github.com/DCgov/dc-madison/blob/master/public/sass/custom/_dc.scss)
which overrides variables and styles.

## Architecture and Development Notes

Madison is a fairly standard Laravel application.

## Localization

* Make a new directory for the language you want to add support for under
  `resources/assets/lang` and copy the files from `resources/assets/lang/en`
  into it.
* Open each PHP file in the new directory and translate the values of each array
  element into the language you're interested in. Note, only translate the
  *values*, do not change the keys of the elements as that will break the
  lookup of the translation.

We encourage you to create new locale files and submit them back to the project!
We hope to support many languages in the future.

### Annotations

TODO: update

* Madison uses [AnnotatorJS](http://annotatorjs.org/) as its Annotation engine.
  This library is only loaded on document pages.

* **Annotator loads and saves all annotations from / to the api**

* We have created an [Annotator
  Plugin](http://docs.annotatorjs.org/en/v1.2.x/hacking/plugin-development.html)
  to add Madison-specific functionality to Annotator and allow Annotator to talk
  to our AngularJS application.
* Madison uses its `public/js/services/annotationService` to handle all
  front-end annotation functionality.  This service is injected into Annotator
  when it is instantiated and allows communication between the Angular
  application and Annotator.

## Contributing

Please include a descriptive message ( and if possible an issue link ) with each pull request.  We will try to merge PRs as quickly as possible.

### Code Style

* Make sure to use the included `.editorconfig` file in your editor.
  Instructions can be found [on the EditorConfig site](http://editorconfig.org/)
  on integrating it with your code editor.
* All PHP code should be styled using `PSR-2` guidelines.  Please make sure to
  run [`php-cs-fixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer) before
  committing code.

### Development Configuration

#### Vagrant
We supply a `Vagrantfile` which should get a working development environment
running without much fuss. This is created via [Homestead](https://laravel.com/docs/5.3/homestead).

For the Vagrant setup to work, your `.env` settings need to be:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

Once those are set
up [install Vagrant](https://www.vagrantup.com/docs/installation/)
and [VirtualBox](https://www.virtualbox.org/) then run `vagrant up` and wait a
bit.

When it is done you should be able to open `192.168.10.10` or `localhost:8000`
in your browser and see Madison.

You can run build commands in the VM (connect with `vagrant ssh` and move to the
shared folder with `cd /vagrant`) since workable versions of the development
tools are installed in there. You can also install the development tools locally
and build there since the directory is shared.

#### Locally
Just running `./artisan serve` will get you going. You will need to setup a
database corresponding to your settings in `.env` by hand though. You will also
likely want to run `make watch` while developing to automatically rebuild the
client whenever changes are made to the SASS or JS files.

### Build Process

* Madison uses a [Makefile](Makefile) to run common tasks, including building
  assets.
* You must have [composer](https://getcomposer.org/) and
  [npm](https://www.npmjs.com/) installed to install client and server
  dependencies.
* To build the application, run `make`. This will run `make deps` and `make
  build`. What happens is:
  * Composer dependencies are installed
  * npm install client dependencies
  * Client files are built into `/public`

### Roadmap

Madison's milestone is organized through its [Github
Milestones](https://github.com/opengovfoundation/madison/milestones).  Please
see milestone descriptions / issues for an up-to-date roadmap.

## Changelog
* `4.0`
  * Remove Angular client code
  * Upgrade to Laravel 5.3
* `3.0`
  * Client and server code has been separated into `client` and `server` folders
  * Built assets are now removed from repo, build happens on deploy
  * Major work towards customization happening in config files and the database
    so multi-instance management can happen from a single core repo
* `2.0`
  * We have upgraded to Laravel 5.1.  Installation is the same, but links have
    been updated to the proper documentation versions.
* `1.8`
  * All configuration options have been moved to [Laravel's `.env`
    files](http://laravel.com/docs/4.2/configuration#protecting-sensitive-configuration).
    All previous `.yml` configuration files have been removed.
  * All templates are now found in the `public/templates` directory.  The only
    blade views left in `app/views` are for emails.
  * All routes not beginning with `api/` are served up `public/index.html`, as a
    [Single Page
    Application](https://en.wikipedia.org/wiki/Single-page_application).  This
    loads the AngularJS app and pulls all data from the API.  All interaction
    between client / server are now done via Angular / the Laravel API.
  * The entire theme has been updated and streamlined.  All relevant assets are
    still included in the `public/sass` directory.  See the [Theming]
    documentation on how to customize the theme for your needs.
  * The build process has been streamlined and tasks have been separated in the
    `tasks` directory.  We've also included concatenation, minification, and
    cache-busting of the relevant JS and CSS assets.
  * All dependencies have been set to a minor version for stability.
