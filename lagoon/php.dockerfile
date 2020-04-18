
FROM amazeeio/php:5.6-fpm

RUN docker-php-ext-install exif

COPY . /app