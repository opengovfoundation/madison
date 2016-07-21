.PHONY: all deps deps-server check-node deps-client build-client clean distclean db-reset db-migrate test test-server test-client selenium-start queue-listen

all: deps build-client

deps: autoload clean deps-server optimize deps-client gems

deps-production: autoload clean deps-server optimize deps-client gems-production

gems:
	gem install bundler --no-rdoc --no-ri && bundle install

gems-production:
	bundle install --without deployment --path vendor/bundle

deps-server:
	cd server && composer install

NODE_VERSION=$(shell node --version | grep -e '^v4\.')

check-node:
ifeq ($(NODE_VERSION),)
	$(error 'You must have node version 4 to run this installer.')
endif

deps-client: check-node
	cd client && npm install

set-key:
	cd server && php artisan key:generate

autoload:
	cd server && composer dump-autoload

optimize:
	cd server && php artisan optimize

build-client:
	cd client && npm run build

test: test-server test-client

test-server: db-test-setup
	cd server && ./vendor/bin/phpunit

test-client: build-client db-test-setup db-test-seed
	cd client && npm run test

selenium-start:
	cd client && webdriver-manager update && webdriver-manager start

clean:
	rm -rf client/.sass-cache
	cd server && php artisan cache:clear

distclean: clean
	rm -rf client/node_modules server/vendor/* client/build/*

db-reset:
	cd server && php artisan db:rebuild && php artisan migrate && php artisan db:seed

db-test-setup:
	cd server && php artisan db:rebuild --database=mysql_testing && php artisan migrate --database=mysql_testing

db-test-seed:
	cd server && php artisan db:seed --database=mysql_testing

db-migrate:
	cd server && php artisan migrate

db-force-seed:
	cd server && php artisan db:seed --force

db-force-migrate:
	echo "Running a forced database migration, potential to lose some data!"
	cd server && php artisan migrate --force

deploy-forge: distclean deps build-client db-force-migrate

queue-listen:
	cd server && php artisan queue:listen

watch:
	cd client && npm run watch

# Chef Commands
# ----------------------------------------------------------

check-server-variable:
	@if [ -z "$(server)" ]; then echo "Must provide a 'server' in user@hostname format." && exit 1; fi

berks:
	berks install && berks vendor config/chef/cookbooks

chef-prepare: check-server-variable
	knife solo prepare $(server) -r "recipe[madison-server::default]"

CONFIG := $(shell echo $(server) | sed -e 's/.*@//')

chef-cook: check-server-variable
	knife solo cook $(server) config/chef/nodes/$(CONFIG).json
