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
	${DOCKER_BASH} ${BIN_CONSOLE} d:s:d --force
	${DOCKER_BASH} ${BIN_CONSOLE} d:s:c

minecraft-import:
	${DOCKER_BASH} ${BIN_CONSOLE} m:i:i /var/www/organizer/public/mcc-items.json
	${DOCKER_BASH} ${BIN_CONSOLE} m:r:i /var/www/organizer/public/mcc-shaped-recipes.json
	${DOCKER_BASH} ${BIN_CONSOLE} m:r:i /var/www/organizer/public/mcc-shapeless-recipes.json

bash:
	docker exec -it organizer-php bash

db-bash:
	docker exec -it organizer-db bash

restart:
	docker-compose down
	docker-compose up -d

pu:
	composer phpunit
