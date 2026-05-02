# Stage 1: Frontend Build
FROM node:22-slim AS frontend-builder

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY resources ./resources
COPY public ./public
COPY vite.config.* ./

RUN npm run build


# Stage 2: Application runtime — official PHP image, no PPA/Launchpad needed
FROM php:8.4-fpm-bookworm

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
    && echo $TZ > /etc/timezone


# System dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    gosu \
    curl \
    ca-certificates \
    zip \
    unzip \
    git \
    sqlite3 \
    dnsutils \
    libcap2-bin \
    libpng-dev \
    libpq-dev \
    libzip-dev \
    libxml2-dev \
    libldap2-dev \
    libicu-dev \
    libreadline-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/*


# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pdo_mysql \
        pdo_sqlite \
        gd \
        zip \
        bcmath \
        soap \
        intl \
        ldap \
        opcache


# PECL extensions
RUN pecl install igbinary msgpack redis \
    && docker-php-ext-enable igbinary msgpack redis \
    && rm -rf /tmp/pear


# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html


# Copy composer files first for better layer caching
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts \
    && rm -rf /root/.composer/cache


# Copy application source
COPY . .

# Copy frontend assets
COPY --from=frontend-builder /app/public/build public/build


# Laravel optimizations
RUN composer dump-autoload --optimize \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache || true


# Configs
COPY docker/production/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/production/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/production/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/production/nginx.conf /etc/nginx/sites-available/default


# Entrypoint
COPY docker/production/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh


# Log file + permissions
RUN install -o www-data -g www-data -m 664 /dev/null /var/log/php-fpm.log

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache


EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
