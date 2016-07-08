# Deploying Madison

For your convenience we have included the necessary tools to easily prepare a
server to run Madison, and to easily deploy Madison to that server once it's
ready.

For these tasks we are using:

* Chef Solo - server provisioning
* Capistrano - deployment

## Installing the tools

You will need to have Ruby installed for all the deployment and provisioning
tools, as well as [bundler](http://bundler.io/). Once those are installed, `cd`
into the project directory and run `bundle`. This will install the necessary
gems for Chef, knife (the Chef command line tool) and Capistrano.

## Provisioning a server

The first thing you will need to do is to create a server. You can use whatever
you'd like here, as long as you have a user on the server that has `sudo`
permissions. Some options here are [Digital Ocean](https://digitalocean.com),
[AWS](https://aws.amazon.com) or [Linode](https://www.linode.com/).

### Installing chef on the server

Once you have the server spun up, run the following command to prepare it for
provisioning with Chef:

```
$ make chef-prepare server=user@hostname
```

This will install the Chef client on the server so that provisioners can be run
there. It will also generate a file at `config/chef/nodes/hostname.json`. This
is the settings file that will be used when running the provisioner.

### User and site configuration

There are a couple configuration files in the `config/chef/data_bags` folder
that you will need to create. Each one has an example file you can use as your
base. They are:

* `config/chef/data_bags/sites/madison.json.example`
* `config/chef/data_bags/users/deploy.json.example`

Copy each one and drop the `.example` from the end of the filename.

#### config/chef/data_bags/sites/madison.json

This file contains configuration settings for the site. This includes:

* `id` - Becomes the name of the application folder on the server
* `site_title` - Base title set for the site instance
* `url` - The URL the site will be accessed at
* `ssl` - Whether to use SSL or not (this will install certs with letsencrypt!)
* `type` - Tells chef what kind of server this is, should always be "Madison"
* `aliases` - A way for you to tag the server node (optional)
* `servers` - Contains the hostname or IP of the server(s) you are deploying to
* `database_name` - The name of the database that will be created
* `database_username` - Username for the database
* `database_password` - Password for the database user

#### config/chef/data_bags/users/you.json

This file is the configuration for your user that will be created on the server.
The attributes you need to update are:

* `id` - The name of your system user
* `password` - This user's login password
* `ssh_keys` - Your public ssh key

**Note**: A `deploy` user will be created as well, and your ssh key entered here
will be added to it's list so deployment with Capistrano can work.

### Running the provisioner

Next, to actually run the provisioners (or as Chef calls it, to "cook" the
server), run the following command:

```
$ make chef-cook server=user@hostname
```

This process can take some time. It will install all necessary dependencies on
the server for the Madison project to run.

## Deploying!

Once the server is provisioned and ready to have Madison launched, you can use
Capistrano to deploy!

### Configuring your deploy target

You will want to create a copy of the file `config/deploy/example.rb` and change
it to something like `config/deploy/production.rb` (or a different stage, or
whatever makes sense to you).

The server name will be the host target for deploying. This can be an IP address
or a hostname.

If you use the Chef instructions above to provision the server, a `deploy` user
will be available already, so you can leave the `user` line alone. Otherwise you
will need to put in the relevant username for this setting.

For the `:deploy_to` setting, this will be the folder on the server that the
site will be deployed to, and should match up with what Apache (or nginx, if you
used that) will point to.

If you used the above Chef instructions to provision, the `:deploy_to` target
should be `/var/www/vhosts/{id_attribute_in_site_data_bag_file}`.

Once this file is all configured, you're ready to deploy!

### Deploy!

Simply running the command `cap {stage} deploy` (where `stage` is the name of the
file you created in `config/deploy/`) will deploy your application! If I created
my deploy target config as `config/deploy/production.rb`, I would run:

```
$ cap production deploy
```

Then you should be able to visit the URL that is pointing at the server and see
a running instance of Madison!

*Note:* For the initial data load you can use `cap {stage} db:seed`.
