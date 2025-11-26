# Stage 1: Frontend Build
FROM node:22-slim as frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
RUN npm run build

# Stage 2: Application
FROM ubuntu:24.04

WORKDIR /var/www/html

# Fix timezone
RUN ln -snf /usr/share/zoneinfo/UTC /etc/localtime && echo UTC > /etc/timezone

# CRITICAL FIX: Added --fix-missing and --no-install-recommends to prevent 404 errors
RUN apt-get update && apt-get install -y --no-install-recommends --fix-missing \
    gnupg \
    gosu \
    curl \
    ca-certificates \
    zip \
    unzip \
    git \
    supervisor \
    sqlite3 \
    libcap2-bin \
    libpng-dev \
    dnsutils \
    php8.3-cli \
    php8.3-fpm \
    php8.3-mysql \
    php8.3-sqlite3 \
    php8.3-curl \
    php8.3-gd \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-zip \
    php8.3-bcmath \
    php8.3-soap \
    php8.3-intl \
    php8.3-readline \
    php8.3-ldap \
    php8.3-msgpack \
    php8.3-igbinary \
    php8.3-redis \
    && mkdir -p /etc/apt/keyrings \
    && php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Copy Frontend Build
COPY --from=frontend-builder /app/public/build public/build

# Copy App Files
COPY . .

# Install PHP Dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

ENTRYPOINT ["/var/www/html/docker/production/entrypoint.sh"]
