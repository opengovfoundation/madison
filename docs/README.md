# Welcome to the Madison Documentation!

## Contents:

* [Introduction]
* Installation
* Theming
* Architecture and Development notes
* Contributing
* Changelog

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
