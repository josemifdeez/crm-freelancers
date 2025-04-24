# Dockerfile optimizado para Laravel + Vite en Render

# --- Build Stage: PHP Dependencies (Composer) ---
    FROM composer:2 as vendor
    WORKDIR /app
    COPY database/ database/
    COPY composer.json composer.lock ./
    COPY package.json package-lock.json ./
    RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader --no-scripts
    
    # --- Build Stage: Frontend Assets (Node + Vite) ---
    FROM node:18 as frontend
    WORKDIR /app
    COPY --from=vendor /app/vendor/ /app/vendor/
    COPY package*.json ./
    RUN npm install
    COPY . .
    RUN npm run build
    
    # --- Final Stage: Production Image (PHP-FPM + Nginx) ---
    FROM php:8.2-fpm-alpine as production
    
    COPY --from=composer /usr/bin/composer /usr/local/bin/composer
    
    RUN apk add --no-cache \
            nginx \
            supervisor \
            php82-fpm php82-pdo php82-pdo_sqlite php82-tokenizer php82-xml \
            php82-ctype php82-fileinfo php82-mbstring php82-openssl php82-bcmath \
            php82-gd php82-zip php82-curl php82-dom php82-session \
            ;
    
    WORKDIR /var/www/html
    
    COPY docker/nginx.conf /etc/nginx/nginx.conf
    COPY docker/supervisord.conf /etc/supervisord.conf
    COPY docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
    
    COPY --from=vendor /app/vendor/ /var/www/html/vendor/
    COPY --from=frontend /app/public/build/ /var/www/html/public/build/
    COPY --from=frontend /app/public/index.php /var/www/html/public/index.php
    COPY . /var/www/html/
    
    # --- CORRECCIÓN: Ejecutar SOLO composer dump-autoload aquí ---
    RUN composer dump-autoload --optimize --no-dev --classmap-authoritative
    # --- Los comandos de Artisan se moverán al Pre-Deploy de Render ---
    
    # Establecer permisos correctos
    RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
        && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
    
    EXPOSE 80
    CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]