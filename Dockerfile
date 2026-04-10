FROM composer:2.8 AS composer-deps

WORKDIR /app

COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts
RUN composer dump-autoload --optimize --no-dev

FROM node:22-alpine AS frontend-build

WORKDIR /app

COPY . .
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi
RUN npm run build

FROM php:8.4-apache AS runtime

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
        libmariadb-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install bcmath exif gd intl mbstring pdo pdo_mysql zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/start-app.sh /usr/local/bin/start-app.sh

COPY --from=composer-deps /app /var/www/html
COPY --from=frontend-build /app/public/build /var/www/html/public/build

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod +x /usr/local/bin/start-app.sh

EXPOSE 80

CMD ["start-app.sh"]