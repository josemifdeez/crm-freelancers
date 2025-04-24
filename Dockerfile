# Dockerfile optimizado para Laravel + Vite en Render (v9 - Fix PDO PgSQL Libs & RUN Syntax)

# --- Build Stage: PHP Dependencies (Composer) ---
    FROM composer:2 as vendor
    WORKDIR /app
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
    
    # Instalar dependencias - Incluir build-deps, runtime libs, y luego limpiar build-deps
    RUN apk add --no-cache \
            nginx \
            supervisor \
            # Dependencias de runtime necesarias para pdo_pgsql
            postgresql-libs \
            # Dependencias necesarias para compilar (temporales)
            build-base \
            postgresql-dev \
            # Paquetes PHP 8.2 base y otras extensiones
            php82 \
            php82-fpm \
            php82-pdo \
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
        # Instalar la extensión pdo_pgsql usando el helper
        && docker-php-ext-install pdo_pgsql \
        # Limpiar solo las dependencias de build después de instalar la extensión
        && apk del build-base postgresql-dev
    
    WORKDIR /var/www/html
    
    COPY docker/nginx.conf /etc/nginx/nginx.conf
    COPY docker/supervisord.conf /etc/supervisord.conf
    COPY docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
    
    COPY --from=vendor /app/vendor/ /var/www/html/vendor/
    COPY --from=frontend /app/public/build/ /var/www/html/public/build/
    COPY --from=frontend /app/public/index.php /var/www/html/public/index.php
    COPY . /var/www/html/
    
    # Ejecutar SOLO composer dump-autoload aquí
    RUN composer dump-autoload --optimize --no-dev --classmap-authoritative
    
    # Crear directorios de storage y cache y establecer permisos
    RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs \
        && chown -R www-data:www-data storage bootstrap/cache \
        && chmod -R 775 storage bootstrap/cache
    
    # Crear directorios para Supervisor y establecer permisos
    RUN mkdir -p /var/log/supervisor /var/run \
        && chown -R www-data:www-data /var/log/supervisor /var/run \
        && chmod -R 775 /var/log/supervisor /var/run
    
    # Copiar y hacer ejecutable el script de inicio (instrucciones separadas y correctas)
    COPY docker/start.sh /usr/local/bin/start-app
    RUN chmod +x /usr/local/bin/start-app
    
    EXPOSE 80
    
    # CMD para usar el script de inicio
    CMD ["start-app"]