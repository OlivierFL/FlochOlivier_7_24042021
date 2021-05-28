.PHONY: up
up:
	@docker-compose up -d
	symfony server:start -d

.PHONY: down
down:
	@symfony server:stop
	@docker-compose down

.PHONY: tests
tests:
	APP_ENV=test symfony console doctrine:database:drop --force
	APP_ENV=test symfony console doctrine:database:create
	APP_ENV=test symfony console doctrine:schema:create
	APP_ENV=test symfony console doctrine:fixtures:load -n
	APP_ENV=test symfony php bin/phpunit

.PHONY: analyze
analyze:
	@codeclimate analyze src
