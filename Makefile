DOCKER_BASH=docker exec -it organizer-php bash
BIN_CONSOLE=bin/console

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
	docker-compose down
	composer install
	docker-compose up -d

bash:
	docker exec -it organizer-php bash

restart:
	docker-compose down
	docker-compose up -d