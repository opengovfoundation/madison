# Welcome to the Madison Documentation!

## Contents:

* [Introduction](#introduction)
* [Installation](#installation)
* [Theming](#theming)
* [Architecture and Development notes](#architecture-and-development-notes)
* [Contributing](#contributing)
* [Changelog](#changelog)

## Introduction:

Madison is an open-source platform built by [The OpenGov Foundation](http://opengovfoundation.org) that facilitates collaboration on policy between citizens, government, and stakeholders.  Madison allows citizens to interact directly with legislation before it becomes law, by commenting, asking questions, and offering improvements directly on legislation under consideration.

**For Citizens:**
This is your chance to tell policymakers how you really feel. Comment, ask questions, or suggest changes directly on legislation before it becomes law. These are your laws; it’s time for you to have your say.

**For Authors**
There’s never been an easier way to get substantive feedback from both colleagues and citizens. Offer and receive input in real time from fellow policymakers, issue experts, and the citizens you represent. With Madison, your job has never been easier.

**For Developers**
Check out the rest of the documentation and read through the [Contributing Guidelines].  Pull requests welcome!

## Installation

Madison is build on top of [Laravel] and uses many of configuration tools that Laravel provides ( specifically its [`.env` files](http://laravel.com/docs/4.2/configuration#protecting-sensitive-configuration) )

### Via Laravel Forge

We recommend using [Laravel Forge](https://forge.laravel.com/) to set up, run, and manage Madison instances.  Forge is a [PaaS](https://en.wikipedia.org/wiki/Platform_as_a_service) product that is tailored specifically to Laravel environments.

1.

### Manually

**Requirements**
Madison and Laravel use Composer for dependency management.  If you don't have it installed, [install Composer first](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).

1.  Clone the repo `git clone git@github.com:opengovfoundation/madison.git`

## Theming

## Architecture and Development Notes

## Contributing

## Changelog

* `1.8`
  * All configuration options have been moved to [Laravel's `.env` files](http://laravel.com/docs/4.2/configuration#protecting-sensitive-configuration).  All previous `.yml` configuration files have been removed.
  * All templates are now found in the `public/templates` directory.  The only blade views left in `app/views` are for emails.
  * All routes not beginning with `api/` are served up `public/index.html`, as a [Single Page Application](https://en.wikipedia.org/wiki/Single-page_application).  This loads the AngularJS app and pulls all data from the API.  All interaction between client / server are now done via Angular / the Laravel API.
  * The entire theme has been updated and streamlined.  All relevant assets are still included in the `public/sass` directory.  See the [Theming] documentation on how to customize the theme for your needs.
  * The build process has been streamlined and tasks have been separated in the `tasks` directory.  We've also included concatenation, minification, and cache-busting of the relevant JS and CSS assets.
  * All dependencies have been set to a minor version for stability.



