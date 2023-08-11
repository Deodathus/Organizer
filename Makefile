DOCKER_BASH=docker exec -it organizer-php
DOCKER_BASH_NON_INTERACTIVE=docker exec organizer-php
BIN_CONSOLE=php bin/console

copy-env:
	cp .env.example .env

build:
	docker-compose build

up:
	docker-compose up -d

down:
	docker-compose down

rebuild:
	docker-compose down
	docker-compose build
	docker-compose up -d
	${DOCKER_BASH} composer install

install:
	${DOCKER_BASH_NON_INTERACTIVE} composer install
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:s:d --force
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:m:m

install-test:
	${DOCKER_BASH_NON_INTERACTIVE} composer install
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:d:c --env=test -n
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:m:m -n
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:m:m --env=test -n

db-migrate:
	${DOCKER_BASH} ${BIN_CONSOLE} d:m:m
	${DOCKER_BASH} ${BIN_CONSOLE} d:m:m --env=test

db-recreate-tables:
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:s:d -n --force --full-database
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:s:d -n --env=test --force --full-database
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:m:m -n
	${DOCKER_BASH_NON_INTERACTIVE} ${BIN_CONSOLE} d:m:m --env=test -n

bash:
	docker exec -it organizer-php bash

db-bash:
	docker exec -it organizer-db bash

restart:
	docker-compose down
	docker-compose up -d

cache-clear:
	${DOCKER_BASH} ${BIN_CONSOLE} cache:clear
	${DOCKER_BASH} ${BIN_CONSOLE} cache:clear --env=test
	${DOCKER_BASH} rm -rf var/cache/*

logs-clear:
	${DOCKER_BASH} truncate -s 0 var/log/dev.log
	${DOCKER_BASH} truncate -s 0 var/log/test.log

pu:
	${DOCKER_BASH_NON_INTERACTIVE} ./vendor/phpunit/phpunit/phpunit

pui:
	${DOCKER_BASH_NON_INTERACTIVE} ./vendor/phpunit/phpunit/phpunit --group integration

pud:
	${DOCKER_BASH} ./vendor/phpunit/phpunit/phpunit --group development
