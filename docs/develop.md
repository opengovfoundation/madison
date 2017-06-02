# Development

For local development, we recommend using [Vagrant](vagrantup.com) with the
included [`Vagrantfile`](/Vagrantfile). This vagrant configuration is based on
[Laravel Homestead](https://laravel.com/docs/5.4/homestead), a prepackaged
Vagrant box that makes setting up a local virtual dev environment simple.

## Setup

The only pre-requisites are Vagrant, and a virtual machine provider:
VirtualBox, VMWare, or Parallels.

### Virtual Machine

Once vagrant is set up on your machine, `cd` to the root of the project
directory and type:

```
$ vagrant up
```

This will create and provision your virtual machine for local development. You
can log into the virtual machine by typing `vagrant ssh` from the project root.

### Configuration

Setting configuration is done in the `.env` file. For an initial setup, copy the
example configuration file with..

```
$ cp .env.example .env
```

The default settings should be what you need for local development. The only
extra things to configure would be [Rollbar](https://rollbar.com) and
[Mailtrap](https://mailtrap.io).

You can also see a [full list of the configuration](/docs/README.md#configuration).

### Emails

We recommend using [Mailtrap](https://mailtrap.io) for development. This is a
free service that allows all emails to reach a common inbox for your inspection.

Simple sign up for an account, then set the appropriate settings in `.env`.

### Database

To set up the database initially, log into the vagrant machine with `vagrant
ssh`, then `cd /vagrant` and run `make db-reset`. This will create, migrate, and
seed the database.

Look in [`config/madison.php`](/config/madison.php) for the initial login
credentials for the seeder users.

## Development

The following items are what you need to know about actively developing this
project. They include building assets, running the queue worker, viewing logs,
and accessing the PHP and database consoles.

### Building Assets

There is an available `npm` task for watching your asset folders and building
when necessary.

```
$ npm run dev
```

This will run gulp in watch mode. To see what all is running, look at the
[`gulpfile.js`](/gulpfile.js).

### Viewing Logs

All log output goes to `storage/logs/laravel.log`.

### Application Consoles

Accessing the database console with the default Vagrant setup can be done with:

```
$ mysql -uroot -psecret homestead
```

And accessing a PHP console:

```
$ php artisan tinker
```
