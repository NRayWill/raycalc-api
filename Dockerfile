FROM composer:latest AS composer
FROM php:7.4.1

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY . /usr/src/raycalc-api
WORKDIR /usr/src/raycalc-api

RUN apt-get update 
RUN apt-get install -y git 
RUN docker-php-ext-install bcmath
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony
# RUN symfony server:ca:install
RUN alias codeception="./vendor/bin/codecept"

CMD symfony server:start -d --no-tls && /bin/bash

EXPOSE 8000
