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

1. Run `composer install` to install all composer packages

1. Copy `server/config/example_creds.php` to `server/config/creds.php`
and add your mysql credentials

1. Copy `.env.example.php` to `.env.php` and add your mysql credentials

1. Run `php artisan migrate` to create database schema

1. Run `npm install` and `bower install` to install the web packages

If you want this to run from the web root instead of http://yoursite.com/madison/public, you must edit your server configuration. For Apache servers:

1. Edit your httpd.conf file to change the DocumentRoot to Madison's public directory (e.g. ```DocumentRoot "/var/www/html/madison/public"```)
1. And allow Madison to handle url rewriting (e.g. ```<Directory "/var/www/html/madison"> AllowOverride All </Directory>```)
1. Restart Apache

You'll need to run `php artisan queue:listen` whenever running the app. See
[the Laravel docs](https;//laravel.com/docs/5.1/queues#running-the-queue-listener)
for more info.
