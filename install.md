#Installation

For Mac OSX installation instructions, see INSTALL.OSX.md.

**Dependencies:**

* PHP ( >= 5.5.9 )
  * libyaml
  * mcrypt
* MySQL
* Laravel ( MVC PHP Framework )
* AngularJS ( MVC JS Framework )
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

1. Copy `server/config/example_creds.php` to `server/config/creds.php`
and add your mysql credentials

1. Copy `server/.env.example` to `server/.env` and add your mysql credentials

1. Create a database based on the settings you set in `server/.env`.

1. Run `make db-migrate` to create database schema.

1. Setup an apache vhost, an example configuration can be seen in `docs/apache.conf.example`.

1. Save the confiration and restart Apache.

You'll need to run `make queue-listen` whenever running the app. See
[the Laravel docs](https;//laravel.com/docs/5.1/queues#running-the-queue-listener)
for more info.
