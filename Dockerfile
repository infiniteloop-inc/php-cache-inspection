# syntax=docker/dockerfile:1

ARG COMPOSER_VERSION=2.2.3
ARG PHP_VERSION=8.1.1
ARG PHP_BASE_IMAGE=bullseye
ARG PHP_EXT_INSTALLER_VERSION=1.4.8

# composer イメージ
FROM composer:${COMPOSER_VERSION} AS vendor

# extension-installer イメージ
FROM mlocati/php-extension-installer:${PHP_EXT_INSTALLER_VERSION} AS ext_installer

# メインイメージ
FROM php:${PHP_VERSION}-cli-${PHP_BASE_IMAGE}

COPY --from=vendor /usr/bin/composer /usr/bin/composer
COPY --from=ext_installer /usr/bin/install-php-extensions /usr/bin/

RUN install-php-extensions apcu memcached opcache pcntl redis zip gd

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp
ENV COMPOSER_CACHE_DIR /tmp

VOLUME ["/var/www/php-cache-inspection"]

WORKDIR /var/www/php-cache-inspection
COPY . /var/www/php-cache-inspection

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "src"]
