# Madison

[![Build Status](https://api.travis-ci.org/opengovfoundation/madison.svg?branch=master)](https://travis-ci.org/opengovfoundation/madison)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/opengovfoundation/madison.svg)](https://scrutinizer-ci.com/g/opengovfoundation/madison?branch=master)

Madison is an open-source, collaborative document editing platform.  While Madison can be used to collaborate on many different kinds of documents, the official version is being built with legislative and policy documents in mind.

If you have questions about Madison, please open an issue and we will try to respond as soon as possible.

Check out the [Madison Documentation](https://github.com/opengovfoundation/madison/tree/master/docs) or jump right into the [Issue Log](https://github.com/opengovfoundation/madison/issues) for more information on the project.

We have created a new, public mailing list for Madison development in Google Groups that you might be interested in subscribing to. We'll be using this as the official channel for all Madison developer news, announcements, and chat from now on. (Though bugs should still be reported here on Github.) You can sign up for it here:

[Madison Mailing List](https://groups.google.com/forum/#!forum/madison-developers)

We have also created a very short survey to find out more about the developers using Madison. Please take a few minutes to fill out this survey so we can better understand what your needs are and who is using Madison:

[Madison Developers Survey](http://goo.gl/forms/BV4Flc0zx7)

## Installation

Please take a look at the [Madison Documentation](https://github.com/opengovfoundation/madison/tree/master/docs) for how to install Madison.

## Customization

There are a few different mechanisms for customizing Madison to your liking.

### Language & i18n

The majority of the language in the system is defined in the `public/locales/`
folder within relevant JSON files, defined for each supported language. If you
create a file that matches the necessary language name in
`public/locales/custom/` this will overwrite what is set as default in the base
language files.

### Style

Any stylesheets placed within the `public/sass/custom/` folder will be processed
during the build and can overwrite any existing styles. You can also change sass
variables here. Reference the sass config files in `public/sass/config/` to see
what variables can be changed.

## Testing Suite

To run the automated test suite locally you will first need to use the
webdriver manager provided by protractor. This allows you to install and run
selenium through a simple cli.

```
$ npm install -g protractor
$ webdriver-manager update
```

To start the selenium server:

```
$ webdriver-manager start
```

Once that is installed, you should be able to run tests for various browsers
using `grunt test_{browser name}` like so:

```
$ grunt test_chrome
$ grunt test_firefox
$ grunt test_ie
```

A database is automatically created in mysql for testing, so no need to create
one.

You will need to have drivers installed for these browsers to test them locally.
When a pull request is created, the tests will be run for all browsers using
[Sauce Labs](https://saucelabs.com/).


## How to help

* Open an issue, claim an issue, comment on an issue, or submit a pull request to resolve one
* Document Madison - wiki documentation, installation guide, or docblocks internally
* Clean up existing code - I'm sure we've taken shortcuts or added lazy code somewhere.
