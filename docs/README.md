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

Madison is an open-source platform built by [The OpenGov Foundation](http://opengovfoundation.org) that facilitates collaboration on policy between citizens, government, and stakeholders.  Madison allows citizens to interact directly with legislation before it becomes law, by commenting, asking questions, and offering improvements directly on legislation under consideration.

**For Citizens:**
This is your chance to tell policymakers how you really feel. Comment, ask questions, or suggest changes directly on legislation before it becomes law. These are your laws; it’s time for you to have your say.

**For Authors:**
There’s never been an easier way to get substantive feedback from both colleagues and citizens. Offer and receive input in real time from fellow policymakers, issue experts, and the citizens you represent. With Madison, your job has never been easier.

**For Developers:**
Check out the rest of the documentation and read through the [Contributing Guidelines](#contributing).  Pull requests welcome!

## Installation

Madison is build on top of [Laravel v.4.2](http://laravel.com/docs/4.2) and uses many of configuration tools that Laravel provides ( specifically its [`.env` files](http://laravel.com/docs/4.2/configuration#protecting-sensitive-configuration) )

### Via Laravel Forge

We recommend using [Laravel Forge](https://forge.laravel.com/) to set up, run, and manage Madison instances.  We use it ourselves at the OpenGov Foundation.  Forge is a [PaaS](https://en.wikipedia.org/wiki/Platform_as_a_service) product that is tailored specifically to Laravel environments.

1.  Create account and log in
1.  Link cloud server account (Linode, Digital Ocean, etc.)
1.  Click `Create Server`.  Forge will then create a server on your linked account with the necessary setup.
1.  Create new site setting your `Root Domain` and keep `public` as the `web directory` setting.
1.  Click `Manage` on your new site in the list of Active Sites below the New Site creation.
1.  Enter your Github repository
1.  Add necessary ENV variables in the `Environment` tab.
  * DB_NAME
  * DB_USER
  * DB_PASS

  *If environment has trouble pulling the credentials, deploy once and update Laravel's `.env` file with the necessary credentials*
1.  Deploy
1.  (Optional) Set up `Quick Deploy`, which will deploy on any pushes to your Github repo.

### Manually

**Requirements**
* `PHP >= 5.4`
* `MCrypt PHP Extension`
* Madison and Laravel use `Composer` for dependency management.  If you don't have it installed, [install Composer first](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).
* See [Laravel Server Requirements](http://laravel.com/docs/4.2/installation#server-requirements) for setting up Laravel applications.

1.  Clone the repo `git clone git@github.com:opengovfoundation/madison.git`
1.  Create database and user
1.  Set ENV variables in [Laravel's `.env` file](http://laravel.com/docs/4.2/configuration#protecting-sensitive-configuration). You can copy the contents from the .env.example file included in this repository
1.  Run `composer install` to get all the dependencies
1.  Run `php artisan migrate` to run all migrations
1.  Make sure `ADMIN_EMAIL` and `ADMIN_PASSWORD` are set in the environment config and then run `php artisan db:seed` to run all database seeds.
1.  Make sure the web server has write access to all folders within `app/storage`
1.  Run `php artisan key:generate` to set the app key

**That's it!**

**Instructions for setting up Madison on Ubuntu: **

* `Install LAMP stack`
    1.	Run `sudo apt-get install apache2` to install Apache server
    1.	Run `sudo apt-get install mysql-server libapache2-mod-auth-mysql php5-mysql` to install MySQL Database and PHP - MySQL module
    1.  Edit the `php.ini` file, add this `extension=mysqli.so` towards the end and save the file.
    1.	Run `sudo apt-get install php5 libapache2-mod-php5` to install PHP5 and the associated modules
    1.	Run `sudo /etc/init.d/apache2 restart` to restart the server

* `Verify if the installation was all okay`
    1.	Open a web browser and navigate to http://localhost/. You should see a message saying It works!
    1.	Check if PHP is working fine: Run `php -r 'echo "\n\nYour PHP installation is working fine.\n\n\n";'`

* `Install & Enable MCrypt extension for PHP`
    1.	Run `sudo apt-get install php5-mcrypt` to install PHP5-Mcrypt
    1.	Run `sudo php5enmod mcrypt` to enable the Mcrypt module for PHP. If you run into issues, take a look at [this] (http://askubuntu.com/a/460862/496364)

* `Install PHP-Curl`
    1.	Run `sudo apt-get install php5-curl`
    1.	Run `sudo service apache2 restart` to restart the server.

After this, follow steps 1 through 7 above to download and setup Madison

## Administration

Administering Madison is pretty simple at this stage of the project.  Any Madison administrator will have extra options in their account dropdown in the top-right corner of the application under `Administrative Dashboard` after logging in.  These options include:

* `Edit Documents`
  * Administrators have access to all documents in Madison.  This option will display all documents with links to the document editor for each.
  * The option to create new documents is found at the bottom of this page.
  * When choosing the "publish state" of a document, the following rules apply:
    * *Published*: Publicly viewable and listed in the front page index
    * *Unpublished*: Only accessible by the sponsor of the document
    * *Private*: Not listed on the front page, but accessible by anyone who has the direct link
* `Verify Accounts`
  * Verified users are presented as such on the public facing side of the application.  This is a way to separate notable accounts from the average registrant.
  * Users request verified status from their `Account Settings` page and must be approved by a site admin.
* `Verify Groups`
  * Groups can be used for different organizations, legislative offices, etc.  Users request to create a group from their `Group Management` page and must be approved by an admin.  Groups have the ability to post new documents on Madison.
* `Verify Independent Sponsors`
  * Users to want to publish documents but are not part of a group can request `Independent Sponsor` status.  They do this from their `Account Settings` page and must be verified by an admin.  After verification, they will be able to post new documents on Madison.
* `Administrative Notification Settings`
  * Site admins may want to be notified when activity is happening on their installation.  This page allows them to subscribe to email notifications for events in Madison.
* `Site Settings`
  * This is where site Admins can change the `Featured Document` on Madison.  This can also be changed in the in`Document Information` tab in each document editor, but gives Admins another option

## Theming

**Note: Madison's styles are built using grunt, via [Compass](http://compass-style.org/) which uses [SASS](http://sass-lang.com/).  You'll need to have a decent knowledge of SASS and CSS to customize the theme currently.**

To customize the Madison design, simply add a new file in the `public/sass/custom/` directory with your custom styles.  All files in this directory will be pulled in automatically [_after_ the global variables are defined](https://github.com/opengovfoundation/madison/blob/master/public/sass/style.scss#L25).  This will allow you to use or override the existing variables, as well as adding custom styles.

The site also has a `.madison` class on the body of the page, so you can override individual style rules by copying any existing rule and adding `.madison` as the top level selector.  For instance, if you want to override the `.navbar` background color, simply apply a new color to `.madison .navbar` instead.

To see a live example, have a look at [DC's custom theme file](https://github.com/DCgov/dc-madison/blob/master/public/sass/custom/_dc.scss) which overrides variables and styles.

## Architecture and Development Notes

### Single Page Application Architecture
* Madison uses a [Single Page Application](https://en.wikipedia.org/wiki/Single-page_application) architecture
* The main routing is done in `app/routes/single.php`.
  * All routes except those starting with `api` are served a single file at `public/index.html`
    * This file loads the compiled `public/build/app.js` and `public/build/app.css` files.
      * These files are cache-busted at build time with an md5 hash query.  The build process is covered below in the [Contribution Guidelines](#contributing)
    * The Angular front-end communicates via API with the Laravel backend.
  * All `api/**` routes reach the Larvel backend.

*If `app.debug` is set, `public/pre-build` is served, and loads all non-minified, non-concatenated assets.*

## Localization

Madison has internationalization support, and but currently only has localization for US English.  To create a new localization, you just need to create a new language file in `public/locales` that matches the name of your language, and then add suitable translations for all the phrases in the English file.

Madison uses the Angular-Translate plugin for frontend translation.  As a result, there are a few oddities:

* In most cases, we're using [the `translate` directive](http://angular-translate.github.io/docs/#/guide/05_using-translate-directive), which results in the content being hidden until it's translated. This also means that html is rendered properly, so it may be used safely in your translations.  The major exception to this is for any html-attribute text that's translated, such as `placeholder` attributes; in these cases, we're using [the `translate` filter](http://angular-translate.github.io/docs/#/guide/04_using-translate-filter) instead.

* In a few places, we need to deal with fancy pluralization/language rules, so we're using [the MessageFormat syntax](http://angular-translate.github.io/docs/#/guide/14_pluralization) in those cases.  In most places, we're using standard interpolation.  This is a bit inconsistent, unfortunately - be careful when creating new locales.

* There are a few outstanding localization issues on the server-side, all covered here: https://github.com/opengovfoundation/madison/issues/513

We encourage you to create new locale files and submit them back to the project!  We hope to support many languages in the future.

### Annotations
* Madison uses [AnnotatorJS](http://annotatorjs.org/) as its Annotation engine.  This library is only loaded on document pages.

* **Annotator loads and saves all annotations from / to the api**

* We have created an [Annotator Plugin](http://docs.annotatorjs.org/en/v1.2.x/hacking/plugin-development.html) to add Madison-specific functionality to Annotator and allow Annotator to talk to our AngularJS application.
* Madison uses its `public/js/services/annotationService` to handle all front-end annotation functionality.  This service is injected into Annotator when it is instantiated and allows communication between the Angular application and Annotator.

### Authentication / Authorization

* `public/js/services/authService` handles all authentication / authorization for the front-end.
  * This service keeps the user updated via its `getUser` method which pulls the current user session from Laravel.
* All page-level authorizations are handled in `public/js/routes.js` in the `authorizedRoles` data attribute.  These are set when the user logs in.
* All user session data is stored and shared across the app via the `public/js/services/sessionService`.
* All app constants ( events and user roles ) can be found in `public/js/constants.js`

### Templates
*All templates are in the `public/templates` directory.*
**Disclaimer: We're still working on getting these organized.**

* `templates/directives`: This directory is for anything being loaded via `templateUrl` in a directive.
* `templates/partials`: This directory is for anything that is included as ng-include as a partial for a view
* `templates/pages`: This directory is for page views being loaded in `routes.js`


### Helpers

We've created a couple helper components devs should be aware of

* Server Side Notifications
  * The front-end is set up to automatically present notifications handled by the controller helper method `growlMessage($messages, $severity, $params)` which can be found in the `app/controllers/BaseController` class.
    * `$messages` is an array of message texts
    * `$severity` is the severity of the messages to pass (`error`, `success`, `info`)
    * `$params` is an array of any extra data needed to pass in the JSON response.
* Front-end helpers
  * `modalService` can be used any time a modal needs to be displayed
  * `prompts` module can be used to add prompt bars at the top of the main content
    * eg. `prompts.info(<html-content>)`
  * `loginPopupService` can be used to display the login modal


## Contributing

Please include a descriptive message ( and if possible an issue link ) with each pull request.  We will try to merge PRs as quickly as possible.

### Code Style

* Make sure to use the included `.editorconfig` file in your editor.  Instructions can be found [on the EditorConfig site](http://editorconfig.org/) on integrating it with your code editor.
* All PHP code should be styled using `PSR-2` guidelines.  Please make sure to run [`php-cs-fixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer) before committing code.

### Development Configuration

By default, Madison loads the `public/index.html` file in a production environment.  If you have `app/debug` set to `true` in your `.env` file, `public/pre-build.html` will be loaded.
  * This file loads all non-minified, non-concatenated dependencies ( many coming from the `public/bower_components` directory )
  **You must install bower ( `npm install -g bower` ) and run `bower install` from the root directory to load all front-end dependencies before running a dev environment.**

### Build Process

* Madison uses [Grunt](http://gruntjs.com/) as its task runner for building assets.
* You must have `npm` installed and run `npm install` to install all dev build dependencied.
* You must also have `bower` installed and run `bower install` to install all front-end dependencies
* All grunt tasks are found in the `tasks` directory.
  * All npm imports can be found in `tasks/config`
  * All custom tasks can be found in `tasks/register`
* The default build command can be run using `grunt build` and will:
  * Minify and concatenate all assets.  All included assets can be found in the `public/pre-build.html` file.
  * Update `public/index.html` with the new hash of the compiled assets for cache-busting

### Roadmap
Madison's milestone is organized through its [Github Milestones](https://github.com/opengovfoundation/madison/milestones).  Please see milestone descriptions / issues for an up-to-date roadmap.

## Changelog

* `1.8`
  * All configuration options have been moved to [Laravel's `.env` files](http://laravel.com/docs/4.2/configuration#protecting-sensitive-configuration).  All previous `.yml` configuration files have been removed.
  * All templates are now found in the `public/templates` directory.  The only blade views left in `app/views` are for emails.
  * All routes not beginning with `api/` are served up `public/index.html`, as a [Single Page Application](https://en.wikipedia.org/wiki/Single-page_application).  This loads the AngularJS app and pulls all data from the API.  All interaction between client / server are now done via Angular / the Laravel API.
  * The entire theme has been updated and streamlined.  All relevant assets are still included in the `public/sass` directory.  See the [Theming] documentation on how to customize the theme for your needs.
  * The build process has been streamlined and tasks have been separated in the `tasks` directory.  We've also included concatenation, minification, and cache-busting of the relevant JS and CSS assets.
  * All dependencies have been set to a minor version for stability.
