FROM php:8.2-apache
RUN docker-php-ext-install mysqli pdo pdo_mysql
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY src/ /var/www/html/
RUN composer install --no-dev --optimize-autoloader
EXPOSE 80
