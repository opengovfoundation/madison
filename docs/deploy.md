# Deploying Madison

## Server Management: Forge

Forge is a service that connects to Linode for server provisioning and
management. It can create a new Linode VPS for you and configure it to run
Laravel (and other PHP) applications. It also allows for easy management of
multiple sites on the same server, including SSL certificates through
LetsEncrypt, databases, and environment file settings.

Start by creating a Forge account, and an account with a VPS provider of your
choice (Linode or Digital Ocean are preferred). The VPS account can be connected
to your Forge account so that Forge can create servers within that VPS provider
account.

When a server is created, you will want to..

* Add your SSH keys by going to the "SSH Keys" area
* Create a MySQL database for the site you're setting up
  * Use `forge` as the database user
  * Create a secure password and be sure to save it for site settings next

Next you need to create a "Site" for this server. Call it what you want and set
the Web Directory to `/current/public`. Envoyer uses a release-based deploy
process, so `/current` always references the currently active release.

Nex you can configure the site settings by going to the "Environment" area.

The site configuration that Forge manages is our `.env` file. A reference to
each of these items can be found in [`docs/README.md`](/docs/README.md). Start
by copying the `.env.example` file in the project root, and configure
accordingly.

You will want to also configure a queue worker by going to the "Queue" section.
The connection should match with what you have configured in `.env` for the
`QUEUE_DRIVER` setting.

Lastly, you will want to configure SSL for this site. Go to the SSL area and
click on LetsEncrypt. Make sure the domain is pointing at the server first,
otherwise this process will fail. Put the domain you plan to use in the Domains
box and click "Obtain Certificate". The process should complete itself! Once
it's installed, click "Activate" to enable it and set up auto-renew.

Once Forge has been used to set up a server and configure a site, it shouldn't
need to be messed with again, except perhaps to change environment settings for
a site, or to renew SSL certificates if you don't set up auto-renew (which is
enabled by default if you use LetsEncrypt).

## Deploy & Release Management: Envoyer

Envoyer is a service for handling release-based deployments of Laravel
applications. Release-based means that each new deploy is built into it's own
new folder and timestamped. This allows for a symlinked folder called `current`
to always point to the latest release. It also allows for easy rollback of a
release by simply changing the symlink.

The Envoyer deploy process is as follows (steps added by us denoted):

1. Clone down the new release into a timestamped release folder.
1. Install composer dependencies.
1. [CUSTOM] Clean the cache, install npm dependencies, and build assets.
  * `make envoyer-post-composer`
1. Activate the new release by switching the symlink to point at it.
1. [CUSTOM] Run database migrations.
  * `make envoyer-post-activate`
1. Purge older releases, based on # we specify to keep around.
1. Perform a health check on the new release.

Envoyer goes a few steps further by enabling this all through a web interface,
and including other features such as auto-deploying from specific branches in
your GitHub repo. When new commits are pushed to the specified branch, Envoyer
will trigger a new deployment. Furthermore, if there are any issues during the
deployment process, the new release will not be used.

To get set up with Envoyer, go to https://envoyer.io and create an account.
Create a project by giving it a name, selecting "Laravel 5/Lumen", and pointing
it at the appropriate GitHub repo.

Next you will need to add a server to the project. This will be the Forge server
that you created above. Give it a name, supply the IP address, in "Connect As"
put "forge", and supply the project path, which should be
`/home/forge/your.domain.here`.

Once this is done, it will give you and SSH key for Envoyer that must be added
to the Forge server. Go back to your Forge server, click on the "SSH Keys"
section, and add that key with an appropriate name.

The last step involves setting a couple Deployment Hooks that are unique to the
Madison project. One for compiling assets, and another for clearing the cache
and compiled views.

The first one, compiling assets, should happen "after" the "Install Composer
Dependencies" action. Click the cog icon for that action, then click "Add Hook"
in the "After This Action" section. Give it the name "Post Composer", run as
"forge", and insert the following code:

```
cd {{release}}
make envoyer-post-composer
```

For the next one, it will go "after" the "Activate New Release" action. Give it
the name "Post Activate". The contents are as follows:

```
cd {{release}}
make envoyer-post-activate
```

As long as you have configured the Envoyer project to deploy from a valid
branch, you should now be good to go! Try doing a manual deployment and you're
off to the races!
