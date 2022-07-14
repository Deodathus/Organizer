DOCKER_BASH=docker exec -it organizer-php bash
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
	composer install
	docker-compose up -d

install:
	docker-compose up -d
	docker exec -it organizer-php bash "php /var/www/organizer/bin/console"

bash:
	docker exec -it organizer-php bash

db-bash:
	docker exec -it organizer-db bash

restart:
	docker-compose down
	docker-compose up -d

pu:
	composer phpunit
