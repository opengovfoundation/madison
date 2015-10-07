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
