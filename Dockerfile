FROM php:8.2-apache

RUN apt-get update
RUN apt-get install -y zip unzip

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-enable pdo_mysql

RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
