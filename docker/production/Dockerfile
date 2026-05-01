# ---------------------------
# Stage 1: Frontend build
# ---------------------------
FROM node:22-slim AS frontend-builder

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY resources ./resources
COPY public ./public
COPY vite.config.* ./

RUN npm run build


# ---------------------------
# Stage 2: Application runtime
# ---------------------------
FROM ubuntu:24.04

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
    && echo $TZ > /etc/timezone


# ---------------------------
# System dependencies
# ---------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
    software-properties-common \
    curl \
    ca-certificates \
    zip \
    unzip \
    git \
    supervisor \
    sqlite3 \
    libpng-dev \
    libpq-dev \
    dnsutils \
    nginx \
    gnupg \
    gosu \
    libcap2-bin \
    && mkdir -p /etc/apt/keyrings \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


# ---------------------------
# PHP 8.4 install
# ---------------------------
# Bypass add-apt-repository (calls Launchpad REST API which times out in CI).
# gpg --recv-keys also fails in BuildKit (no dirmngr daemon). Fetch key via curl instead.
RUN curl -fsSL "https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x14AA40EC0831756756D7F66C4F4EA0AAE5267A6C" \
        | gpg --dearmor -o /usr/share/keyrings/ondrej-php.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/ondrej-php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu noble main" \
        > /etc/apt/sources.list.d/ondrej-php.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        php8.4-cli \
        php8.4-fpm \
        php8.4-pgsql \
        php8.4-mysql \
        php8.4-sqlite3 \
        php8.4-curl \
        php8.4-gd \
        php8.4-mbstring \
        php8.4-xml \
        php8.4-zip \
        php8.4-bcmath \
        php8.4-soap \
        php8.4-intl \
        php8.4-readline \
        php8.4-ldap \
        php8.4-msgpack \
        php8.4-igbinary \
        php8.4-redis


# ---------------------------
# Composer
# ---------------------------
RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/bin --filename=composer


# ---------------------------
# Copy composer first (cache layer)
# ---------------------------
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts \
    && rm -rf /root/.composer/cache


# ---------------------------
# Copy application source
# ---------------------------
COPY . .


# ---------------------------
# Copy frontend assets
# ---------------------------
COPY --from=frontend-builder /app/public/build public/build


# ---------------------------
# Run Laravel optimizations
# ---------------------------
RUN composer dump-autoload --optimize \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache || true


# ---------------------------
# Configs
# ---------------------------
COPY docker/production/supervisord.conf \
    /etc/supervisor/conf.d/supervisord.conf

COPY docker/production/php.ini \
    /etc/php/8.4/cli/conf.d/99-custom.ini

COPY docker/production/php.ini \
    /etc/php/8.4/fpm/conf.d/99-custom.ini

COPY docker/production/www.conf \
    /etc/php/8.4/fpm/pool.d/www.conf

COPY docker/production/nginx.conf \
    /etc/nginx/sites-available/default


# ---------------------------
# Entrypoint
# ---------------------------
COPY docker/production/entrypoint.sh \
    /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh


# ---------------------------
# Logs + permissions
# ---------------------------
RUN install -o www-data -g www-data -m 664 \
    /dev/null /var/log/php8.4-fpm.log

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache


# ---------------------------
# Expose
# ---------------------------
EXPOSE 80


ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
