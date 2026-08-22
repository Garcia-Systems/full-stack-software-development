FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install
COPY frontend ./frontend
COPY tsconfig.json tsconfig.app.json tsconfig.node.json vite.config.ts eslint.config.js ./
RUN npm run build

FROM composer:2 AS dependencies
WORKDIR /app
COPY composer.json ./
RUN composer install --no-interaction --prefer-dist --no-scripts
COPY . .
COPY --from=frontend /app/public/build /app/public/build
RUN mkdir -p bootstrap/cache storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && chmod -R u+rwX bootstrap/cache storage \
    && composer dump-autoload --optimize
FROM php:8.4-cli
RUN docker-php-ext-install pdo_mysql
WORKDIR /app
COPY --from=dependencies /app /app
ARG APP_VERSION=development
ENV APP_VERSION=${APP_VERSION}
EXPOSE 8080
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
