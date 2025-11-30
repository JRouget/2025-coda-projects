FROM dunglas/frankenphp:php8.3-alpine

RUN set -eux; \
    install-php-extensions \
    pdo_mysql \
    ;