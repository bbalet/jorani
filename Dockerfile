FROM composer as composer
COPY composer.json composer.lock ./
RUN composer install --ignore-platform-reqs --no-dev

FROM php:apache
RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --from=composer /app/vendor /var/www/html/vendor
COPY . .
COPY docker/database.php docker/email.php application/config/
