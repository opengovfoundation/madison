.PHONY: all deps deps-php clean distclean db-reset db-migrate test test-php queue-listen

all: deps

deps: deps-php optimize autoload gems

deps-production: deps-php optimize autoload gems-production

gems:
	gem install bundler --no-rdoc --no-ri && bundle install --path vendor/bundle

gems-production:
	bundle install --without deployment --path vendor/bundle

deps-php:
	composer install

set-key:
	php artisan key:generate

autoload:
	composer dump-autoload

optimize:
	php artisan optimize

test: test-php

test-php: db-test-setup
	./vendor/bin/phpunit

clean:
	rm -rf .sass-cache
	php artisan cache:clear

distclean:
	rm -rf .sass-cache vendor/*

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

queue-listen:
	php artisan queue:listen
