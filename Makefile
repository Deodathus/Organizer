DOCKER_BASH=docker exec -it organizer-php
BIN_CONSOLE=php bin/console

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
	${DOCKER_BASH} composer install
	${DOCKER_BASH} ${BIN_CONSOLE} d:s:d --force
	${DOCKER_BASH} ${BIN_CONSOLE} d:m:m

install-test:
	${DOCKER_BASH} ${BIN_CONSOLE} d:d:c --env=test
	${DOCKER_BASH} ${BIN_CONSOLE} d:s:d --force --env=test
	${DOCKER_BASH} ${BIN_CONSOLE} d:m:m
	${DOCKER_BASH} ${BIN_CONSOLE} d:m:m --env=test

db-migrate:
	${DOCKER_BASH} ${BIN_CONSOLE} d:m:m
	${DOCKER_BASH} ${BIN_CONSOLE} d:m:m --env=test

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
	${DOCKER_BASH} ./vendor/phpunit/phpunit/phpunit

pui:
	${DOCKER_BASH} ./vendor/phpunit/phpunit/phpunit --group integration

pud:
	${DOCKER_BASH} ./vendor/phpunit/phpunit/phpunit --group development
