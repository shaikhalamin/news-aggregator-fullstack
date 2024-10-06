
FROM php:8.2-fpm

WORKDIR /var/www/html

RUN apt update && apt install -y zlib1g-dev libicu-dev libzip-dev zip unzip libpng-dev libssl-dev pkg-config supervisor

RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install zip intl pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.* ./

COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install
RUN composer dump-autoload --no-scripts
RUN php artisan key:generate
RUN chown -R www-data:www-data /var/www/html/storage/logs

EXPOSE 9000
CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port=8000"]