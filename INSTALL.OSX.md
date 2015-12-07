# Installation on OSX

1. Install XCode + CLI tools, if not already done.

2. Install [homebrew](homebrew).

3. Make sure you have a LAMP (Apache, MySQL, PHP) installation running. Madison has not been tested
with Nginx. MySQL drop-in alternatives like MariaDB may work, but have not been tested. PHP 5.3+ is required.
Additionally, make sure your PHP installation includes the mcrypt and libyaml modules.
  1. With homebrew, you can do `brew install php56 php56-mcrypt`
  2. Make sure in your apache settings that your apache config is loading the
  `php5` module from you homebrew installed version, like so:
     * `LoadModule php5_module /usr/local/php5/libphp5.so`

4. Install [composer](composer)
  1. Or just put this in your terminal: `curl -sS https://getcomposer.org/installer | php`

5. Install node and npm. We recommend using [nvm](nvm)
  1. `curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.29.0/install.sh | bash`
  2. Restart your terminal
  3. `nvm install stable`
  4. `nvm alias default stable && nvm use default`

6. Install [bower](bower) (easiest through npm). Yes, that's 3 package managers we've installed.
  1. `npm install -g bower`

7. Run `npm install` so that it can grab the packages in packages.json.

8. Run `composer install` to install all our PHP dependencies.

9. Run `grunt install` which will run some more install commands,
downloading and installing the required packages. You may run into an issue
with Github's API ratelimiting your requests for composer install, [see this
workaround](https://coderwall.com/p/kz4egw).
  1. If you don't have the `grunt` command, do `npm install -g grunt-cli`

10. Install compass. If you do not have any ruby gems installed, you can get away with
`sudo gem update --system` and `sudo gem install compass`, installing the compass gem
on top of Mac OSX's ruby.
  1. In general, you will want to use something like [rvm](rvm) to manage a separate version
  of ruby which does not interfere with OSX. If you use rvm, drop the `sudo` from
  the aforementioned commands.

11. Set up the site as an Apache vhost.

12. Create a clean database in MySQL.

13. Rename `example_creds.php` to `creds.php` and enter your DB info, as well as
admin info for the initial admin user seed. Additionally, do the same for
`example_smtp.php` if you want to enable email functionality (required for
registering new users).

12. Run `php artisan migrate` and `php artisan db:seed` from the root directory to
set up the database.

13. Finally, install and run elasticsearch. [See here for installation](elasticsearch).
  1. Additionally, run `curl -XPUT http://localhost:9200/madison` when ES is running
  to create a new index.

### Note

When editing any .js files, make sure to run `grunt watch` so that it can watch for and
re-minify (among other things) the main build/app.js and build/app.map files.

[elasticsearch]: https://gist.github.com/rajraj/1556657
[homebrew]: http://brew.sh/
[composer]: https://getcomposer.org/
[bower]: http://bower.io/
[nvm]: https://github.com/creationix/nvm
[rvm]: http://rvm.io
