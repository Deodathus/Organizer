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
	${DOCKER_BASH} composer install
	docker-compose up -d

install:
	${DOCKER_BASH} composer install
	${DOCKER_BASH} bin/console d:s:d --force
	${DOCKER_BASH} bin/console d:s:c

bash:
	docker exec -it organizer-php bash

db-bash:
	docker exec -it organizer-db bash

restart:
	docker-compose down
	docker-compose up -d

pu:
	composer phpunit
