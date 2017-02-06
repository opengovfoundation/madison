.PHONY: all build build-prod deps deps-php clean distclean db-reset db-migrate test test-php gettext-prep queue-listen watch

all: deps build

build:
	./node_modules/.bin/gulp

build-prod:
	./node_modules/.bin/gulp --production

deps: deps-node deps-php

deps-production: deps-node deps-php

deps-node:
	npm install

deps-php:
	composer install

set-key:
	php artisan key:generate

test: test-php

test-php: db-test-setup
	./vendor/bin/phpunit

clean:
	rm -rf public/build public/css public/js
	@-php artisan cache:clear
	@-php artisan view:clear
	@-php artisan config:clear
	@-php artisan route:clear

distclean: clean
	rm -rf node_modules vendor/*

db-reset:
	php artisan db:rebuild && php artisan migrate && php artisan db:seed

db-test-setup:
	php artisan db:rebuild --database=mysql_testing && php artisan migrate --database=mysql_testing

db-test-seed:
	php artisan db:seed --database=mysql_testing

db-migrate:
	php artisan migrate

db-force-seed:
	php artisan db:seed --force

db-force-migrate:
	echo "Running a forced database migration, potential to lose some data!"
	php artisan migrate --force

db-backup:
	php artisan db:backup

db-restore:
	@if [ -z "$(file)" ]; then echo "Must provide a 'file' option." && exit 1; fi
	php artisan db:restore $(file)

deploy-forge: distclean deps-production db-force-migrate

gettext-prep:
	php artisan gettext:update

queue-listen:
	php artisan queue:listen

watch:
	./node_modules/.bin/gulp watch

envoyer-post-composer: clean deps-node build-prod
	php artisan config:cache
	php artisan route:cache

envoyer-post-activate: db-force-migrate
