FROM php:7.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
  zip unzip curl git \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  libzip-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
