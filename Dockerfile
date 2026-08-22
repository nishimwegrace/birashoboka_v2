ARG PHP_VERSION=8.3
FROM php:${PHP_VERSION}-apache

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev \
        zip \
        unzip \
        git \
        zlib1g-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libwebp-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install pdo pdo_mysql zip gd \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

# Apply custom PHP ini overrides (upload limits, display_errors off, etc.)
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
