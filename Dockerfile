FROM php:8.4-fpm AS app_php

RUN apt-get update && apt-get install -y \
    default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql fileinfo \
    && rm -rf /var/lib/apt/lists/*

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions zip

COPY php.ini /usr/local/etc/php/conf.d/uploads.ini

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN mkdir -p /var/www/html/uploads/audio && \
    chmod -R 777 /var/www/html/uploads