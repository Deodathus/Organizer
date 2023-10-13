DOCKER_BASH=docker exec -it organizer-php
DOCKER_BASH_NON_INTERACTIVE=docker exec organizer-php
BIN_CONSOLE=php bin/console

ORGANIZER_AUTH_DOCKER_COMPOSE_LOCATION=~/projects/organizer-auth/docker-compose.yml
ORGANIZER_AUTH_FE_DOCKER_COMPOSE_LOCATION=~/projects/organizer-auth-fe/docker-compose.yml
ORGANIZER_FINANCE_FE_DOCKER_COMPOSE_LOCATION=~/projects/organizer-finance-fe/docker-compose.yml

.PHONY: copy-env
copy-env:
	cp .env.example .env

.PHONY: build
build:
	docker-compose build

.PHONY: up
up:
	docker-compose up -d

.PHONY: down
down:
	docker-compose down

.PHONY: rebuild
rebuild:
	docker-compose down
	docker-compose build
	docker-compose up -d
	${DOCKER_BASH} composer install

.PHONY: install
install:
	${DOCKER_BASH_NON_INTERACTIVE} composer install
	${DOCKER_BASH_NON_INTERACTIVE} composer require --working-dir=tools/php-cs-fixer friendsofphp/php-cs-fixer
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:s:d --force
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:m:m

.PHONY: install-test
install-test:
	${DOCKER_BASH_NON_INTERACTIVE} composer install
	${DOCKER_BASH_NON_INTERACTIVE} composer require --working-dir=tools/php-cs-fixer friendsofphp/php-cs-fixer
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:d:c --env=test -n
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:m:m -n
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:m:m --env=test -n

.PHONY: db-migrate
db-migrate:
	${DOCKER_BASH} ${BIN_CONSOLE} d:m:m
	${DOCKER_BASH} ${BIN_CONSOLE} d:m:m --env=test

.PHONY: db-recreate-tables
db-recreate-tables:
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:s:d -n --force --full-database
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:s:d -n --env=test --force --full-database
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:m:m -n
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:m:m --env=test -n

.PHONY: bash
bash:
	docker exec -it organizer-php bash

.PHONY: db-bash
db-bash:
	docker exec -it organizer-db bash

.PHONY: restart
restart:
	docker-compose down
	docker-compose up -d

.PHONY: cache-clear
cache-clear:
	${DOCKER_BASH} ${BIN_CONSOLE} cache:clear
	${DOCKER_BASH} ${BIN_CONSOLE} cache:clear --env=test
	${DOCKER_BASH} rm -rf var/cache/*

.PHONY: logs-clear
logs-clear:
	${DOCKER_BASH} truncate -s 0 var/log/dev.log
	${DOCKER_BASH} truncate -s 0 var/log/test.log

.PHONY: pu
pu:
	${DOCKER_BASH_NON_INTERACTIVE} ./vendor/phpunit/phpunit/phpunit

.PHONY: pui
pui:
	${DOCKER_BASH_NON_INTERACTIVE} ./vendor/phpunit/phpunit/phpunit --group integration

.PHONY: pud
pud:
	${DOCKER_BASH} ./vendor/phpunit/phpunit/phpunit --group development

.PHONY: phpcs
phpcs:
	${DOCKER_BASH_NON_INTERACTIVE} bash tools/php-cs-fixer-fix.sh --dry-run

.PHONY: phpcs-fix
phpcs-fix:
	${DOCKER_BASH_NON_INTERACTIVE} bash tools/php-cs-fixer-fix.sh

.PHONY: phpstan
phpstan:
	${DOCKER_BASH_NON_INTERACTIVE} vendor/bin/phpstan analyse --memory-limit=1024M

.PHONY: phpstan-generate-baseline
phpstan-generate-baseline:
	${DOCKER_BASH_NON_INTERACTIVE} vendor/bin/phpstan --generate-baseline

.PHONY: deptracCheck
deptracCheck:
	${DOCKER_BASH_NON_INTERACTIVE} bash tools/deptrac.sh

.PHONY: install-with-minecraft-data
install-with-minecraft-data: install minecraft-import

.PHONY: minecraft-import
minecraft-import: minecraft-items-import minecraft-shaped-recipes-import minecraft-shapeless-recipes-import

.PHONY: minecraft-items-import
minecraft-items-import:
	${DOCKER_BASH} ${BIN_CONSOLE} m:i:i /var/www/organizer/public/mcc-items.json

.PHONY: test-minecraft-items-import
test-minecraft-items-import:
	${DOCKER_BASH} ${BIN_CONSOLE} m:i:i /var/www/organizer/public/mcc-items-test.json

.PHONY: minecraft-shaped-recipes-import
minecraft-shaped-recipes-import:
	${DOCKER_BASH} ${BIN_CONSOLE} m:r:i /var/www/organizer/public/mcc-shaped-recipes.json

.PHONY: minecraft-shapeless-recipes-import
minecraft-shapeless-recipes-import:
	${DOCKER_BASH} ${BIN_CONSOLE} m:r:i /var/www/organizer/public/mcc-shapeless-recipes.json

.PHONY: minecraft-gt-recipes-import
minecraft-gt-recipes-import:
	${DOCKER_BASH} ${BIN_CONSOLE} m:gt-r:i /var/www/organizer/public/mcc-gt-recipes.json

.PHONY: up-everything
up-everything:
	docker-compose up -d
	docker-compose -f ${ORGANIZER_AUTH_DOCKER_COMPOSE_LOCATION} up -d
	docker-compose -f ${ORGANIZER_AUTH_FE_DOCKER_COMPOSE_LOCATION} up -d
	docker-compose -f ${ORGANIZER_FINANCE_FE_DOCKER_COMPOSE_LOCATION} up -d

.PHONY: down-everything
down-everything:
	docker-compose -f ${ORGANIZER_AUTH_DOCKER_COMPOSE_LOCATION} down
	docker-compose -f ${ORGANIZER_AUTH_FE_DOCKER_COMPOSE_LOCATION} down
	docker-compose -f ${ORGANIZER_FINANCE_FE_DOCKER_COMPOSE_LOCATION} down
	docker-compose down

