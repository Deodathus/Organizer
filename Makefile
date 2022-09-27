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
	${DOCKER_BASH} ${BIN_CONSOLE} d:s:c

install-with-minecraft-data: install minecraft-import

minecraft-import: minecraft-items-import minecraft-shaped-recipes-import minecraft-shapeless-recipes-import

minecraft-items-import:
	${DOCKER_BASH} ${BIN_CONSOLE} m:i:i /var/www/organizer/public/mcc-items.json

test-minecraft-items-import:
	${DOCKER_BASH} ${BIN_CONSOLE} m:i:i /var/www/organizer/public/mcc-items-test.json

minecraft-shaped-recipes-import:
	${DOCKER_BASH} ${BIN_CONSOLE} m:r:i /var/www/organizer/public/mcc-shaped-recipes.json

minecraft-shapeless-recipes-import:
	${DOCKER_BASH} ${BIN_CONSOLE} m:r:i /var/www/organizer/public/mcc-shapeless-recipes.json

minecraft-gt-recipes-import:
	${DOCKER_BASH} ${BIN_CONSOLE} m:gt-r:i /var/www/organizer/public/mcc-gt-recipes.json

bash:
	docker exec -it organizer-php bash

db-bash:
	docker exec -it organizer-db bash

restart:
	docker-compose down
	docker-compose up -d

pu:
	composer phpunit
