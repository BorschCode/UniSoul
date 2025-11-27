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

# Install base dependencies first
RUN apt-get update && apt-get install -y --no-install-recommends \
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
    libpq-dev \
    dnsutils \
    software-properties-common \
    && mkdir -p /etc/apt/keyrings \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Add ondrej/php repository for PHP 8.4
RUN curl -fsSL https://packages.sury.org/php/apt.gpg -o /etc/apt/keyrings/php.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/php.gpg] https://packages.sury.org/php/ noble main" > /etc/apt/sources.list.d/php.list \
    && apt-get update

# Install PHP 8.4 with all required extensions
RUN apt-get install -y --no-install-recommends \
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
    php8.4-redis \
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

# Copy supervisor config
COPY docker/production/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy PHP config
COPY docker/production/php.ini /etc/php/8.4/cli/conf.d/99-custom.ini

# Copy and set up entrypoint
COPY docker/production/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
