#Installation

For Mac OSX installation instructions, see INSTALL.OSX.md.

**Dependencies:**

* PHP ( >= 5.5.9 )
  * libyaml
  * mcrypt
* MySQL
* Laravel ( MVC PHP Framework )
* Composer ( PHP Dependency Manager )
  * composer.json
* NPM ( Node Dependency Manager )
  * package.json

1. Install [Composer](http://getcomposer.org/)

1. Install and enable the yaml php extension
  * CentOS
    * `yum install libyaml libyaml-devel`
    * `pecl install yaml`

1. Run `make deps` to install all dependencies.

1. Copy `config/example_creds.php` to `config/creds.php`
and add your mysql credentials

1. Copy `.env.example` to `.env` and add your mysql credentials

1. Create a database based on the settings you set in `.env`.

1. Run `make db-reset` to create database schema and seed it with data.

1. Setup an apache vhost, an example configuration can be seen in
   [docs/apache.conf.example](docs/apache.conf.example).

1. Save the confiration and restart Apache.

You'll need to run `make queue-listen` whenever running the app. See
[the Laravel docs](https;//laravel.com/docs/5.1/queues#running-the-queue-listener)
for more info.

To make sure Sass files are compiled upon change, also run `make watch`.
