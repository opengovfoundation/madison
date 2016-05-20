# Madison

[![Build Status](https://api.travis-ci.org/opengovfoundation/madison.svg?branch=master)](https://travis-ci.org/opengovfoundation/madison)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/opengovfoundation/madison.svg)](https://scrutinizer-ci.com/g/opengovfoundation/madison?branch=master)

Madison is an open-source, collaborative document editing platform.  While
Madison can be used to collaborate on many different kinds of documents, the
official version is being built with legislative and policy documents in mind.

If you have questions about Madison, please open an issue and we will try to
respond as soon as possible.

Check out the [Madison Documentation](https://github.com/opengovfoundation/madison/tree/master/docs)
or jump right into the [Issue Log](https://github.com/opengovfoundation/madison/issues)
for more information on the project.

We have created a new, public mailing list for Madison development in Google
Groups that you might be interested in subscribing to. We'll be using this as
the official channel for all Madison developer news, announcements, and chat
from now on. (Though bugs should still be reported here on Github.) You can sign
up for it here:

[Madison Mailing List](https://groups.google.com/forum/#!forum/madison-developers)

We have also created a very short survey to find out more about the developers
using Madison. Please take a few minutes to fill out this survey so we can
better understand what your needs are and who is using Madison:

[Madison Developers Survey](http://goo.gl/forms/BV4Flc0zx7)

## Architecture

As of 3.0, Madison has been broken into two separate pieces: a standalone
API in Laravel (PHP) (the `server` folder) and a piece for the interface / client
side of the system (Angular / JavaScript) (the `client` folder).

Some modules are shared across repositories, such as our
[internationalization locale files](https://github.com/opengovfoundation/madison-locales).

## Installation

Please take a look at the `docs/INSTALL.md` or `docs/INSTALL.OSX.md` files for
how to install Madison.

## Customization

There are a few different mechanisms for customizing Madison to your liking.

### Language & i18n

The majority of the language in the system is defined in the `client/app/locales/`
folder within relevant JSON files, defined for each supported language. If you
create a file that matches the necessary language name in
`client/app/locales/custom/` this will overwrite what is set as default in the base
language files.

### Style

Any stylesheets placed within the `client/app/sass/custom/` folder will be processed
during the build and can overwrite any existing styles. You can also change sass
variables here. Reference the sass config files in `client/app/sass/config/` to see
what variables can be changed.

## Testing Suite

To run the full test suite for both client and server, run the `make test`
command. Some setup is necesary, see below.

### Server Testing

To run the tests, you will need to make sure to have a database created called
`madison_testing`. Then you will need to run all migrations on that database
with the command `php artisan migrate --database=mysql_testing`.

To run only server tests, the command is `make test-server`.

### Client Testing

For client testing to work properly, you need to have a vhost set up to be
serving the client at `localhost:8888` with the API (server folder)
mounted at `/api`. This vhost should also set two ENV vars:

* `TESTING=true`
* `DB_CONNECTION=mysql_testing`

For an example of what this config should look like for Apache, look at
`travis/apache.conf`. This is the configuration file that is used
for running client tests on travis. Don't forget to add the domain to
`/etc/hosts` as well!

Make sure you have run `make deps` to have all dependencies installed. Once that
is done you should be able to run `make selenium-start` to install and start
your local Selenium server.

To run the client tests only, the command is `make test-client`.

## How to help

* Open an issue, claim an issue, comment on an issue, or submit a pull request to resolve one
* Document Madison - wiki documentation, installation guide, or docblocks internally
* Clean up existing code - I'm sure we've taken shortcuts or added lazy code somewhere.
