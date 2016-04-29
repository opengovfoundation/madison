.PHONY: all deps deps-server deps-client build-client clean distclean db-reset db-migrate test test-server test-client

all: install build-client

deps: clean deps-server autoload optimize deps-client

deps-server:
	cd server && composer install

deps-client:
	cd client && npm install

autoload:
	cd server && composer dump-autoload

optimize:
	cd server && php artisan optimize

build-client:
	cd client && npm run build

test: test-server test-client

test-server:
	cd server && ./vendor/bin/phpunit

test-client:
	cd client && npm run test

clean:
	rm -rf client/.sass-cache
	cd server && composer clear-cache

distclean: clean
	rm -rf client/node_modules server/vendor/* client/build/*

db-reset:
	cd server && php artisan db:rebuild && php artisan migrate && php artisan db:seed

db-migrate:
	cd server && php artisan migrate

deploy-forge: distclean deps build-client db-migrate

watch:
	cd client && npm run watch
