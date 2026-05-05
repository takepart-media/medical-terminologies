FROM serversideup/php:8.4-fpm-nginx-alpine AS base

USER root

RUN install-php-extensions intl bcmath pdo_pgsql

RUN apk add --no-cache nano htop bash-completion postgresql-client && \
    printf 'alias ll="ls -la"\nalias la="ls -A"\n' > /etc/profile.d/aliases.sh

USER www-data


FROM base AS production

COPY --chown=www-data:www-data . /var/www/html
