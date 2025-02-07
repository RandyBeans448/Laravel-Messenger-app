# Use official PHP 8.2 FPM image (adjust if needed)
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

# Clear the apt cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions commonly required by Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer (copied from the official Composer image)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Laravel code (useful in production; for dev we typically mount instead)
COPY . /var/www/html

# Install PHP dependencies (Composer)
RUN composer install --no-dev --optimize-autoloader

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
