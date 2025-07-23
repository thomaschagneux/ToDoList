# Makefile

.PHONY: reset-db

reset-db:
	@echo "Dropping the dev database..."
	php bin/console doctrine:database:drop --force --if-exists

	@echo "Creating the dev database..."
	php bin/console doctrine:database:create

	@echo "Running migrations on dev database..."
	php bin/console doctrine:migrations:migrate --no-interaction

	@echo "Loading fixtures in dev database..."
	php bin/console doctrine:fixtures:load --no-interaction

reset-test-db:
	@echo "Dropping the test database..."
	php bin/console doctrine:database:drop --env=test --force --if-exists

	@echo "Creating the test database..."
	php bin/console doctrine:database:create --env=test

	@echo "Running migrations on test database..."
	php bin/console doctrine:migrations:migrate --env=test --no-interaction

	@echo "Loading fixtures in test database..."
	php bin/console doctrine:fixtures:load --env=test --no-interaction

clean:
	clear
	php bin/console cache:clear
	php bin/console cache:warm
	./vendor/bin/php-cs-fixer fix
	./vendor/bin/phpstan analyse

test:
	clear
	php bin/console cache:clear --env=test
	php bin/console cache:warm --env=test
	php vendor/bin/phpunit --testdox --coverage-html var/reports/
