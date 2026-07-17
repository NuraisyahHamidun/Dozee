# Stage 1: Build frontend assets
FROM node:18-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Build PHP environment and Laravel app
FROM php:8.2-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    nginx \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    bash \
    icu-dev \
    libxml2-dev

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip pdo_mysql opcache bcmath intl soap

# Copy Composer from the official image
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Copy built assets from Node stage
COPY --from=node-builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Create necessary directories and set permissions
RUN mkdir -p storage/framework/cache/data \
    && mkdir -p storage/framework/app \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/logs \
    && mkdir -p storage/certs \
    && curl -sS https://api.aiven.io/v1/ca.pem -o storage/certs/ca.pem \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data /var/www/html

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Fix line endings of entrypoint script (in case written with Windows CRLF)
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

# Expose port
EXPOSE 8080

# Run entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
