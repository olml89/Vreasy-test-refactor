DOCKER_COMPOSE = docker-compose -f docker/docker-compose.yml --env-file .env

.PHONY: build
build:
	$(DOCKER_COMPOSE) build

.PHONY: upd
upd:
	$(DOCKER_COMPOSE) -f docker/docker-compose.yml up -d --remove-orphans

.PHONY: down
down:
	$(DOCKER_COMPOSE) -f docker/docker-compose.yml down

.PHONY: ssh
ssh:
	$(DOCKER_COMPOSE) -f docker/docker-compose.yml exec php-apache /bin/sh