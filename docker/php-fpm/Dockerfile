FROM composer:latest as composer
FROM php:8.3-fpm

RUN apt-get update && apt-get install -y libmcrypt-dev libpng-dev mariadb-client \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libgd-dev \
    && docker-php-ext-install pdo_mysql

RUN docker-php-ext-configure exif
RUN docker-php-ext-install exif
RUN docker-php-ext-enable exif
RUN docker-php-ext-install pcntl

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-webp=/usr/include/  --with-jpeg=/usr/include/
RUN docker-php-ext-install -j$(nproc) gd

RUN apt install jpegoptim optipng pngquant gifsicle libavif-bin -y

RUN apt-get install -y wget unzip curl
RUN curl -sS https://getcomposer.org/installer |php
RUN mv composer.phar /usr/local/bin/composer

COPY ./conf.d /usr/local/etc/php/conf.d/

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
RUN /usr/local/bin/composer self-update

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN groupadd -g ${GROUP_ID} www || true
RUN useradd -u ${USER_ID} -ms /bin/sh -g www www || true

#RUN groupadd -g 1000 www
#RUN useradd -u 1000 -ms /bin/sh -g www www
USER www

WORKDIR /var/www/app



