# Laravel 12 requires PHP 8.2+
FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libpng-dev libonig-dev zlib1g-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Set file permissions (Render runs as root)
RUN chmod -R 777 storage bootstrap/cache

# Render sets $PORT automatically (no need to expose)
ENV PORT=10000

# If using Vite, ensure you have built assets BEFORE deploy
# (npm run build on your machine)
# Commit public/build to your repo so Vite assets load

CMD bash -lc " \
    if [ ! -f .env ]; then cp .env.example .env; fi; \
    php artisan key:generate --force; \
    php artisan migrate --force || true; \
    php artisan storage:link || true; \
    php artisan serve --host=0.0.0.0 --port=${PORT} \
"
