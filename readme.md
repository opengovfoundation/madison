## Madison

Madison is an open-source, collaborative document editing platform.  While Madison can be used to collaborate on many different kinds of documents, the official version is being built with legislative and policy documents in mind.

Right now Madison is in a _pre-stable_ version.  A roadmap to version 2.0 (stable) has been laid out in milestones, and can be viewed in the project's issue log.

If you have questions about Madison, please open an issue and we will try to respond as soon as possible.

Check out the [Madison Wiki](https://github.com/opengovfoundation/madison/wiki) or jump right into the [Madison Architecture](https://github.com/opengovfoundation/madison/wiki/madison-architecture/) for more information on the project.

## Installation

1.  Install [Composer](http://getcomposer.org/)
1.  Install and enable the yaml php extension
	* CentOS
		* `yum install libyaml libyaml-devel`
		* `pecl install yaml`
	* OSX
		* `brew install libyaml`
		* Follow the homebrew instructions for enabling the extension
1.  run `composer install` to install all composer packages
1. 	copy `app/config/example_creds.yml` to `app/config/creds.yml` and add your mysql credentials
1.  run `php artisan migrate` to create database schema

## How to help

* Claim an issue, comment on an issue, or submit a pull request to resolve one
* Document Madison - wiki documentation, installation guide, or docblocks internally
* Clean up existing code - I'm sure we've taken shortcuts or added lazy code somewhere.  Can you find them all?