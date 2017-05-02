# Madison: Development Lifecycle and CI Pipeline

The following outlines the tools and processes we use for development,
deployment, automated builds, and production monitoring on Madison.

For information on deploying an instance of Madison yourself, using Forge and
Envoyer, check [our deployment documentation](docs/deploy.md).

## Pipeline

1. Code is pushed up for review as a "diff" in Phabricator.
2. CircleCI automatically runs the build process, reporting back any failure.
3. If build succeeds and changes are accepted, the author then "lands" the
   code onto the `master` branch.
  1. Builds that are unsuccessful create an obstruction towards landing that
     particular diff, ensuring we always have passing builds.
4. Envoyer will detect new commits on the target branch and trigger a new
   release to be deployed to all instances tracking that branch.
  1. Our staging site will be configured to track `master`, and production
     instances will be set up to track a `production` branch.
  2. At a regular interval (typically at the end of a sprint) we will merge
     `master` to `production` which will trigger production deploys.
5. If deploy process completes successfully, we are notified via Slack.
6. Any errors that happen in production will be reported via Rollbar.

## Tools

### Development Workflow: Phabricator

Phabricator is our tool of choice for repo hosting, task management, code
review, and general development workflow needs. We use a combination of project
workboards and tasks (similar to GitHub issues) for our day-to-day workflow of
development progress.

Phabricator also connects this process to other tools described below. For
example, each time a "diff" is created, it triggers a build process on that new
code within CircleCI. It also syncs specific branches to GitHub which allows for
Envoyer to kick off auto-deploys from those branches, if configured.

### Build and Test Runner: CircleCI

CircleCI is a service for running our application's build process, which
includes running our test suite. Phabricator depends on this service to
determine if a particular diff under review should be allowed to merge or not.
If the build does not pass, Phabricator will restrict (or at least warn you)
from landing the diff into the target branch.

### Server Management: Forge

Though we do use Forge, it's not part of our CI/CD pipeline. For more
information, check out [our deployment documentation](docs/deploy.md).

### Deploy & Release Management: Envoyer

Envoyer is a tool for doing automated release-based deployments. For more
information on Envoyer, including how we have it configured, visit the
[deployment documentation](docs/deploy.md).

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

### Production Monitoring: Rollbar

Rollbar is a service for capturing errors that happen in production and putting
them into a nice web interface. It includes notification functionality through
multiple channels so we are notified when problems occur. The web interface
includes contextual information such as the browser and OS of the user who
experienced the error, how many times it has occurred and how often, and more.
