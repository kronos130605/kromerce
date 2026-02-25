FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    nginx \
    supervisor

RUN docker-php-ext-install pdo pdo_pgsql mbstring bcmath opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www/html
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Create before build
RUN mkdir -p public/build

# Give permissions
RUN chown -R www-data:www-data public/build

# Install Node.js dependencies and build assets
RUN npm install \
    && rm -rf public/build \
    && npm run build \
    && ls -R public/build


# Clear Laravel log file for fresh deploy
RUN mkdir -p storage/logs \
    && truncate -s 0 storage/logs/laravel.log \
    && chown -R www-data:www-data storage bootstrap/cache

COPY ./nginx.conf /etc/nginx/sites-available/default
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD php artisan migrate --force && supervisord -n
