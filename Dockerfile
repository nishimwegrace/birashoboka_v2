
ARG PHP_VERSION=8.3
FROM php:${PHP_VERSION}-apache

ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update \
	&& apt-get install -y --no-install-recommends libzip-dev zip unzip git zlib1g-dev \
	&& docker-php-ext-install pdo pdo_mysql zip \
	&& rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

WORKDIR /var/www/html

# copy project files (no Composer install; dependencies expected bundled or not required)
COPY . .

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
