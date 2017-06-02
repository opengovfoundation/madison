# Madison Release Notes

## 4.0

* Transitioned to a traditional Laravel application.
* Removed Angular code from front-end, all server rendered now.
* Major UX and UI overhaul.
* Improved notifications system.
* Many more new improvements and features.

**Note:** This is a major release! To upgrade, we recommend setting up a new
server and migrating over the old database. For deploy steps, check the
[deployment documentation](/docs/deploy.md).

## 3.0

* Chef solo setup and cookbooks for easy server provisioning.
* Capistrano setup for easy deployment and other remote tasks.
* Makefile with common convenience tasks.
* New file structure to separate client and server code.
* No longer committing built client assets to git.
* Customize Madison instances without forking the repo.
  * Includes style overrides and locale file overrides.
* Simplified document sponsorship model.

**Note:** For documentation on this release, upgrading from older versions, and
more, check the [version 3
branch](https://github.com/opengovfoundation/madison/tree/v3).
