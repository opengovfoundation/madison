# Installation on OSX

1. Install XCode + CLI tools, if not already done.

1. Install [homebrew](homebrew).

1. Make sure you have a LAMP (Apache, MySQL, PHP) installation running. Madison has not been tested
with Nginx. MySQL drop-in alternatives like MariaDB may work, but have not been tested. PHP 5.5.9+ is required.
Additionally, make sure your PHP installation includes the mcrypt and libyaml modules.
  1. With homebrew, you can do `brew install php56 php56-mcrypt`
  1. Make sure in your apache settings that your apache config is loading the
  `php5` module from you homebrew installed version, like so:
     * `LoadModule php5_module /usr/local/php5/libphp5.so`

1. Install [composer](composer)
  1. Or just put this in your terminal: `curl -sS https://getcomposer.org/installer | php`

1. Install node and npm. We recommend using [nvm](nvm)
  1. `curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.29.0/install.sh | bash`
  1. Restart your terminal
  1. `nvm install stable`
  1. `nvm alias default stable && nvm use default`
  1. Update npm version using `npm install -g npm`

1. Run `make deps`, this will install both client and server dependencies.

1. Install compass. If you do not have any ruby gems installed, you can get away with
`sudo gem update --system` and `sudo gem install compass`, installing the compass gem
on top of Mac OSX's ruby.
  1. In general, you will want to use something like [rvm](rvm) to manage a separate version
  of ruby which does not interfere with OSX. If you use rvm, drop the `sudo` from
  the aforementioned commands.

1. Set up the site as an Apache vhost. An example can be seen in
   [docs/apache.conf.example](docs/apache.conf.example).

1. Create a clean database in MySQL.

1. Rename `server/config/example_creds.php` to `server/config/creds.php` and
enter your DB info, as well as admin info for the initial admin user seed.
Additionally, do the same for `example_smtp.php` if you want to enable email
functionality (required for registering new users).

1. Run `make db-reset` to get the database migrated and seeded with data.

1. Copy the `server/.env.example` file to `server/.env` and set the appropriate variables.
  1. If you don't have one already, set up an account at [MailTrap](mailtrap)
  and set the `MAIL_USERNAME` and `MAIL_PASSWORD` variables to what it provides
  for your test inbox. This is needed for user signup.

### Note

When editing any .scss files, make sure to run `make watch` so that it can watch for and
re-compile the main css files.

[homebrew]: http://brew.sh/
[composer]: https://getcomposer.org/
[bower]: http://bower.io/
[nvm]: https://github.com/creationix/nvm
[rvm]: http://rvm.io
