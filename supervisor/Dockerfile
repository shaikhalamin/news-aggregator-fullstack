FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

RUN docker-php-ext-install pdo pdo_mysql intl

RUN apk update && apk add --no-cache supervisor

# Install dependencies for Composer
RUN apk add --no-cache curl git unzip 
# zlib1g-dev libicu-dev libzip-dev zip libpng-dev libssl-dev pkg-config

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN mkdir -p "/etc/supervisor/logs"

COPY supervisord.conf /etc/supervisor/supervisord.conf

# CMD ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisor/supervisord.conf"]

# Dockerfile for backend-service


