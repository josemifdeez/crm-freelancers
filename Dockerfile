# Dockerfile optimizado para Laravel + Vite en Render (v6 - PostgreSQL)

# --- Build Stage: PHP Dependencies (Composer) ---
    FROM composer:2 as vendor
    WORKDIR /app
    # No necesitamos copiar 'database/' si no usamos SQLite en el build
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
    
    # Instalar dependencias PHP y Nginx - ASEGURAR pdo_pgsql, QUITAR pdo_sqlite
    RUN apk add --no-cache \
            nginx \
            supervisor \
            php82-fpm \
            php82-pdo \
            php82-pdo_pgsql \ 
            php82-tokenizer \
            php82-xml \
            php82-ctype \
            php82-fileinfo \
            php82-mbstring \
            php82-openssl \
            php82-bcmath \
            php82-gd \
            php82-zip \
            php82-curl \
            php82-dom \
            php82-session \
            # Opcional: php82-redis, php82-pcntl
            ;
    
    WORKDIR /var/www/html
    
    # Copiar archivos de configuración (sin cambios)
    COPY docker/nginx.conf /etc/nginx/nginx.conf
    COPY docker/supervisord.conf /etc/supervisord.conf
    COPY docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
    
    # Copiar artefactos de las etapas de build
    COPY --from=vendor /app/vendor/ /var/www/html/vendor/
    COPY --from=frontend /app/public/build/ /var/www/html/public/build/
    COPY --from=frontend /app/public/index.php /var/www/html/public/index.php
    
    # Copiar el resto del código de la aplicación (SIN la carpeta database si ya no necesitas SQLite)
    COPY . /var/www/html/
    
    # Ejecutar SOLO composer dump-autoload aquí
    RUN composer dump-autoload --optimize --no-dev --classmap-authoritative
    
    # Crear directorios de storage y cache si no existen
    RUN mkdir -p /var/www/html/storage/framework/sessions \
                 /var/www/html/storage/framework/views \
                 /var/www/html/storage/framework/cache/data \
                 /var/www/html/storage/logs \
        && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
        && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
    
    # Crear directorios para Supervisor
    RUN mkdir -p /var/log/supervisor /var/run \
        && chown -R www-data:www-data /var/log/supervisor /var/run \
        && chmod -R 775 /var/log/supervisor /var/run
    
    # Copiar y hacer ejecutable el script de inicio
    COPY docker/start.sh /usr/local/bin/start-app
    RUN chmod +x /usr/local/bin/start-app
    
    EXPOSE 80
    
    # CMD para usar el script de inicio
    CMD ["start-app"]