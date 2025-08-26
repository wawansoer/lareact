#==========================================================
#         Stage 1 - Base PHP image with extensions        #
#==========================================================
ARG PHP_VERSION=8.4

FROM php:${PHP_VERSION}-cli-alpine AS laravel-app

ARG WWWUSER=1000
ARG WWWGROUP=1000
ARG USER=octane
ARG WORKDIR=/var/www/html

ARG APP_ENV=production
ARG OCTANE_PORT=8000
ARG TZ=Asia/Jakarta

ENV TZ=${TZ:-Asia/Jakarta} \
    APP_ENV=${APP_ENV:-production} \
    USER=${USER:-octane} \
    WORKDIR=${WORKDIR:-/var/www/html} \
    WWWUSER=${WWWUSER:-1000} \
    WWWGROUP=${WWWGROUP:-1000}

WORKDIR ${WORKDIR}

SHELL ["/bin/sh", "-eou", "pipefail", "-c"]

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime \
    && echo ${TZ} > /etc/timezone

# Add PHP extension installer
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install permanent dependencies
RUN apk add --no-cache tzdata bash shadow

# Install PHP extensions and remove build dependencies
RUN apk add --no-cache --virtual .build-deps \
    autoconf g++ make git \
    && install-php-extensions bz2 pcntl pdo_pgsql exif zip uv vips intl redis igbinary swoole \
    && docker-php-source delete \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/* /var/tmp/*

# Create the www-data user
RUN addgroup -g ${WWWGROUP} ${USER} \
    && adduser -D -h ${WORKDIR} -G ${USER} -u ${WWWUSER} -s /bin/sh ${USER}

# Install Composer globally
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

USER ${USER}

#==========================================================
#         Stage 2 - Install Composer dependencies         #
#==========================================================
FROM laravel-app AS vendor

ENV APP_ENV=${APP_ENV:-production}
# Install PHP dependencies using composer, excluding development dependencies and optimizing for production
WORKDIR /app

# Copy only files needed for composer install
COPY --link --chown=${WWWUSER}:${WWWGROUP} composer.* ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-autoloader \
    --no-ansi \
    --no-scripts \
    --audit \
    && composer clear-cache


#==========================================================
#          Stage 3 - Build Front End assets               #
#==========================================================

FROM oven/bun:alpine AS node

ENV APP_ENV=${APP_ENV:-production} \
    NODE_ENV=${APP_ENV:-production}

WORKDIR /app

# Copy only files needed for bun install
COPY --link package.json bun.lock* ./

RUN bun install

# Copy source files and vendor directory
COPY --link . .
COPY --link --from=vendor /app/vendor vendor
RUN bun run build:ssr

#==========================================================
#         Stage 4 - Final application image               #
#==========================================================
FROM laravel-app

ARG WORKDIR
ARG APP_ENV

ENV APP_ENV=${APP_ENV:-production} \
    NODE_ENV=${APP_ENV:-production} \
    WORKDIR=${WORKDIR:-/var/www/html} \
    BUN_RUNTIME_TRANSPILER_CACHE_PATH=0

USER root

WORKDIR ${WORKDIR}

# Copy PHP configuration
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-octane.ini

# Copy application files
COPY --chown=${USER}:${USER} . .

# Copy dependencies from previous stages
COPY --link --chown=${WWWUSER}:${WWWGROUP} --from=vendor /app/vendor ./vendor

# Copy Front End Assets & Runtime For SSR
COPY --link --chown=${WWWUSER}:${WWWGROUP} --from=node /app/public/build ./public/build
COPY --link --chown=${WWWUSER}:${WWWGROUP} --from=node /app/bootstrap/ssr /app/bootstrap/ssr
COPY --link --from=node /usr/local/bin/bun /usr/local/bin/bun
RUN chmod +x /usr/local/bin/bun

# Remove unnecessary files
RUN rm -rf \
.git \
.env.example \
.gitattributes \
.gitignore \
docker-compose.yml \
tests/ 

# Create necessary directories and set permissions
RUN mkdir -p \
storage/framework/sessions \
storage/framework/views \
storage/framework/cache \
storage/framework/testing \
storage/logs \
bootstrap/cache \
    && chmod -R a+rw storage bootstrap/cache \
    && chown -R ${WWWUSER}:${WWWGROUP} storage bootstrap/cache \
    && touch storage/logs/.gitkeep \
    && chmod 777 storage/logs
    
# Install S6-Overlay
ENV S6_OVERLAY_VERSION=v3.2.0.2
ENV S6_BEHAVIOUR_IF_STAGE2_FAILS=2

RUN case "$(uname -m)" in \
"aarch64") S6_ARCH="aarch64" ;; \
"armv7l")  S6_ARCH="armhf" ;; \
"armv6l")  S6_ARCH="armhf" ;; \
    "i386")    S6_ARCH="i686" ;; \
    "x86_64")  S6_ARCH="x86_64" ;; \
    "i486")    S6_ARCH="i486" ;; \
    "i686")    S6_ARCH="i686" ;; \
    *) echo "Unsupported architecture"; exit 1 ;; \
    esac \
    && mkdir -p /tmp/s6-overlay \
    && curl -sSL https://github.com/just-containers/s6-overlay/releases/download/${S6_OVERLAY_VERSION}/s6-overlay-noarch.tar.xz -o /tmp/s6-overlay/s6-overlay-noarch.tar.xz \
    && curl -sSL https://github.com/just-containers/s6-overlay/releases/download/${S6_OVERLAY_VERSION}/s6-overlay-${S6_ARCH}.tar.xz -o /tmp/s6-overlay/s6-overlay-${S6_ARCH}.tar.xz \
    && curl -sSL https://github.com/just-containers/s6-overlay/releases/download/${S6_OVERLAY_VERSION}/s6-overlay-symlinks-noarch.tar.xz -o /tmp/s6-overlay/s6-overlay-symlinks-noarch.tar.xz \
    && curl -sSL https://github.com/just-containers/s6-overlay/releases/download/${S6_OVERLAY_VERSION}/s6-overlay-symlinks-arch.tar.xz -o /tmp/s6-overlay/s6-overlay-symlinks-arch.tar.xz \
    && tar -Jxf /tmp/s6-overlay/s6-overlay-noarch.tar.xz -C / \
    && tar -Jxf /tmp/s6-overlay/s6-overlay-${S6_ARCH}.tar.xz -C / \
    && tar -Jxf /tmp/s6-overlay/s6-overlay-symlinks-noarch.tar.xz -C / \
    && tar -Jxf /tmp/s6-overlay/s6-overlay-symlinks-arch.tar.xz -C / \
    && rm -rf /tmp/s6-overlay
    
    # Copy S6-Overlay configuration
COPY docker/s6-overlay/ /etc/s6-overlay/
   
# Change the user
USER ${USER}

# Install composer dependencies with optimization
RUN composer install \
    --classmap-authoritative \
    --no-interaction \
    --no-ansi \
    --no-dev \
    && composer clear-cache
    
RUN bun install -p --no-cache --ignore-scripts
# Expose ports
EXPOSE ${OCTANE_PORT}

ENTRYPOINT ["/init"]