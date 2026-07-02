FROM php:8.3-fpm

ARG WWWUSER=1000
ARG WWWGROUP=1000

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl \
        bcmath \
        gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY php.ini /usr/local/etc/php/conf.d/custom.ini

RUN printf '[www]\nlisten = 0.0.0.0:9000\n' > /usr/local/etc/php-fpm.d/zz-listen.conf

RUN groupmod -o -g ${WWWGROUP} www-data \
    && usermod -o -u ${WWWUSER} -g www-data www-data \
    && mkdir -p /var/www/.composer \
    && chown -R www-data:www-data /var/www

WORKDIR /var/www/html

USER www-data
