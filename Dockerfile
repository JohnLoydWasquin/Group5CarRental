# ------------ Base PHP Image ------------
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl unzip nginx libzip-dev libpng-dev libonig-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set proper permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Expose port 80 for Render
EXPOSE 80

# Start Nginx + PHP-FPM
CMD service nginx start && php-fpm
