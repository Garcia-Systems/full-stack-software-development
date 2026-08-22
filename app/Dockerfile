FROM composer:2 AS dependencies
WORKDIR /app
COPY composer.json ./
RUN composer install --no-interaction --prefer-dist --no-scripts
COPY . .
RUN composer dump-autoload --optimize
FROM php:8.4-cli
RUN docker-php-ext-install pdo_mysql
WORKDIR /app
COPY --from=dependencies /app /app
EXPOSE 8080
CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force --class=Database\\Seeders\\DatabaseSeeder >/dev/null 2>&1 || true; php artisan serve --host=0.0.0.0 --port=8080"]
