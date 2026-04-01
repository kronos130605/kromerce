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

# Copy PHP configuration for production
COPY .docker/php.ini /usr/local/etc/php/php.ini

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
    && rm -f public/build/manifest.webmanifest \
    && ls -R public/build

# Ensure storage directory structure exists with correct permissions
RUN mkdir -p storage/app/public \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/logs \
    && mkdir -p public/storage


# Clear Laravel log file for fresh deploy and fix storage permissions
RUN mkdir -p storage/logs \
    && truncate -s 0 storage/logs/laravel.log 2>/dev/null || true \
    && chown -R www-data:www-data storage \
    && chown -R www-data:www-data bootstrap/cache \
    && chmod -R 775 storage \
    && chmod -R 775 bootstrap/cache

# Create storage symlink if it doesn't exist
RUN if [ ! -L public/storage ]; then \
        php artisan storage:link 2>/dev/null || ln -s /var/www/html/storage/app/public /var/www/html/public/storage; \
    fi

COPY ./.docker/nginx.conf /etc/nginx/sites-available/default
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]
