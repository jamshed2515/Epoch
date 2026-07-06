# Stage 1: Build frontend assets using Node.js
FROM node:20-alpine AS asset-builder
WORKDIR /app
COPY . .
RUN npm ci && npm run build

# Stage 2: Production PHP/Nginx container
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    git \
    unzip \
    libxml2-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    sqlite-dev \
    postgresql-dev \
    bash

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_sqlite \
    pdo_pgsql \
    zip \
    gd \
    opcache \
    bcmath

# Copy composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application code
COPY . .

# Copy built frontend assets from Stage 1
COPY --from=asset-builder /app/public/build ./public/build

# Copy custom Nginx configuration
COPY nginx.conf /etc/nginx/http.d/default.conf

# Modify Nginx to run as www-data instead of nginx
RUN sed -i 's/user nginx;/user www-data;/g' /etc/nginx/nginx.conf

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Remove Windows CRLF line endings from entrypoint script and make it executable
RUN sed -i 's/\r$//g' /var/www/html/docker-entrypoint.sh \
    && chmod +x /var/www/html/docker-entrypoint.sh

# Expose port 8080
EXPOSE 8080

# Define entrypoint
ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
