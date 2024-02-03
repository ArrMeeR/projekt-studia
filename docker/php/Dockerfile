FROM php:8.3-fpm-alpine

WORKDIR /app

RUN mkdir -p /app/var/cache && mkdir -p /app/var/log && chown -R www-data:www-data /app/var

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions pdo_mysql intl

COPY --from=composer /usr/bin/composer /usr/bin/composer
