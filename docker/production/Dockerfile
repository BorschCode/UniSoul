# Multi-stage build for UniSoul Laravel Application (Raspberry Pi ARM64 compatible)

# Stage 1: Build frontend assets
FROM node:22-slim AS frontend-builder

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy source files needed for build
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./

# Build assets
RUN npm run build


# Stage 2: Build production image
FROM ubuntu:24.04

LABEL maintainer="UniSoul Team"
LABEL app="unisoul"

ARG MYSQL_CLIENT="mysql-client"
ARG POSTGRES_VERSION=18

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV SUPERVISOR_PHP_COMMAND="/usr/bin/php -d variables_order=EGPCS /var/www/html/artisan octane:start --server=swoole --host=0.0.0.0 --port=80"
ENV SUPERVISOR_PHP_USER="www-data"

# Set timezone
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Install base dependencies
RUN apt-get update \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev dnsutils \
    && mkdir -p /etc/apt/keyrings \
    && rm -rf /var/lib/apt/lists/*

# Add PHP repository
RUN curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x14aa40ec0831756756d7f66c4f4ea0aae5267a6c' | gpg --dearmor | tee /etc/apt/keyrings/ppa_ondrej_php.gpg > /dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu noble main" > /etc/apt/sources.list.d/ppa_ondrej_php.list

# Install PHP 8.4 and extensions
RUN apt-get update \
    && apt-get install -y \
       php8.4-cli php8.4-dev \
       php8.4-pgsql php8.4-sqlite3 php8.4-gd \
       php8.4-curl php8.4-imap php8.4-mysql \
       php8.4-mbstring php8.4-xml php8.4-zip \
       php8.4-bcmath php8.4-soap php8.4-intl \
       php8.4-readline php8.4-ldap \
       php8.4-msgpack php8.4-igbinary \
       php8.4-redis php8.4-swoole php8.4-memcached \
    && php -v \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer \
    && composer --version

# Add PostgreSQL repository and install database clients
RUN curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor | tee /etc/apt/keyrings/pgdg.gpg >/dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/pgdg.gpg] http://apt.postgresql.org/pub/repos/apt noble-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && apt-get update \
    && apt-get install -y $MYSQL_CLIENT postgresql-client-$POSTGRES_VERSION \
    && rm -rf /var/lib/apt/lists/*

# Allow PHP to bind to port 80
RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.4

# Copy application files
COPY --chown=www-data:www-data . /var/www/html

# Copy built frontend assets from frontend-builder stage
COPY --from=frontend-builder --chown=www-data:www-data /app/public/build /var/www/html/public/build

# Install Composer dependencies (production only)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Supervisor config
COPY docker/8.4/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# PHP config
COPY docker/8.4/php.ini /etc/php/8.4/cli/conf.d/99-sail.ini

# Start script
RUN echo '#!/usr/bin/env bash\n\
if [ ! -d /.composer ]; then\n\
    mkdir /.composer\n\
fi\n\
\n\
chmod -R ugo+rw /.composer\n\
\n\
if [ $# -gt 0 ]; then\n\
    exec gosu www-data "$@"\n\
else\n\
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf\n\
fi' > /usr/local/bin/start-container \
    && chmod +x /usr/local/bin/start-container

EXPOSE 80/tcp

ENTRYPOINT ["start-container"]
