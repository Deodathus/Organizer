DOCKER_BASH=docker-compose exec organizer-php

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

start:
	docker-compose up -d
	$(DOCKER_BASH) composer install
	$(DOCKER_BASH) bin/console d:s:c

bash:
	docker exec -it organizer-php bash