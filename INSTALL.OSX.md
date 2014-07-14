##Installation on OSX

1. Make sure you have a LAMP (Apache, MySQL, PHP) installation running. Madison has not been tested
with Nginx. MySQL drop-in alternatives like MariaDB may work, but have not been tested. PHP 5.3+ is required.
Additionally, make sure your PHP installation includes the mcrypt and libyaml modules.
2. Install Xcode + CLI tools, if not already done.
3. Install homebrew. Although this is optional, it will make things faster and easier.
4. Install composer. See https://getcomposer.org/, or through homebrew.
5. Install node and npm. http://nodejs.org/, or through homebrew.
6. Install bower (easiest through npm). Yes, that's 3 package managers we've installed.
7. Run npm install so that it can grab the packages in packages.json.
8. Run `grunt install` which will run some more install commands, 
downloading and installing the required packages. You may run into an issue
with Github's API ratelimiting your requests for composer install, see this workaround
https://coderwall.com/p/kz4egw.
9. Install compass. If you do not have any ruby gems installed, you can get away with
`sudo gem update --system` and `sudo gem install compass`, installing the compass gem
on top of Mac OSX's ruby. In general, you will want to use something like RVM to manage
a separate version of ruby which does not interfere with OSX.

10. Set up the site as an Apache vhost, create a clean database in MySQL. Rename
example_creds.yml to creds.yml and enter your DB info, as well as admin info
for the initial admin user seed. Additionally, do the same for example_smtp.yml
if you want to enable email functionality (required for registering new users).

11. Run `php artisan migrate` and `php artisan db:seed` from the root directory to 
set up the database.

12. Finally, install and run elasticsearch. See here for installation - https://gist.github.com/rajraj/1556657
. Additionally, run `curl -XPUT http://localhost:9200/madison` when ES is running
to create a new index.

When editing any .js files, make sure to run `grunt` so that it can watch for and 
re-minify (among other things) the main build/app.js and build/app.map files.

