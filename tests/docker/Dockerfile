FROM php:7.1.4-alpine

ARG HOST_USER_ID
ARG HOST_GROUP_ID

ENV PHP_IDE_CONFIG="serverName=sitemaps"

RUN apk --update --no-cache add \
        autoconf \
        build-base \
        bzip2-dev \
        zlib-dev \
        git \
        unzip \
        bash

# PECL extensions
RUN pecl install xdebug-2.5.3 && \
    docker-php-ext-enable xdebug

# Composer
RUN curl --show-error https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/bin

COPY ./tests/docker/scripts/xdebug /usr/local/bin/xdebug

RUN addgroup -g ${HOST_GROUP_ID} app && \
    adduser -s /bin/sh/ -D -u ${HOST_USER_ID} -G app app && \
    mkdir -p /app/src && \
    chown -R app:app /app/ && \
    echo "xdebug.remote_enable=1"                                               >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.remote_autostart=0"                                            >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.remote_port=9000"                                              >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.remote_connect_back=0"                                         >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.idekey=PHPSTORM"                                               >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.remote_host=$(/sbin/ip route|awk '/default/ { print $3 }')"    >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

USER app

VOLUME /app
WORKDIR /app
