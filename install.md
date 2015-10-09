#Installation

For Mac OSX installation instructions, see INSTALL.OSX.md. 

**Dependencies:**
* PHP ( >= 5.5.9 )
  * libyaml
  * mcrypt
* MySQL
* Laravel ( MVC PHP Framework )
* AngularJS ( MVC JS Framework )
* Grunt ( JS Task Runner )
  * Gruntfile.js
* Composer ( PHP Dependency Manager )
  * composer.json
* Bower ( Front-end Dependency Manager )
  * bower.json
* NPM ( Node Dependency Manager )
  * package.json


1.  Install [Composer](http://getcomposer.org/)
1.  Install and enable the yaml php extension
  * CentOS
    * `yum install libyaml libyaml-devel`
    * `pecl install yaml`
  * OSX
    * `brew install libyaml`
    * Follow the homebrew instructions for enabling the extension
1.  run `composer install` to install all composer packages
1.  copy `app/config/example_creds.php` to `app/config/creds.php` and add your mysql credentials
1.  copy `.env.example.php` to `.env.php` and add your mysql credentials
1.  run `php artisan migrate` to create database schema
1.  run `npm install` and `bower install` to install the web packages

If you want this to run from the web root instead of http://yoursite.com/madison/public, you must edit your server configuration. For Apache servers:

1. Edit your httpd.conf file to change the DocumentRoot to Madison's public directory (e.g. ```DocumentRoot "/var/www/html/madison/public"```)
2. And allow Madison to handle url rewriting (e.g. ```<Directory "/var/www/html/madison"> AllowOverride All </Directory>```)
3. Restart Apache
