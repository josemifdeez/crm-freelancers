# Dockerfile optimizado para Laravel + Vite en Render

# --- Build Stage: PHP Dependencies (Composer) ---
    FROM composer:2 as vendor

    WORKDIR /app
    COPY database/ database/
    COPY composer.json composer.lock ./
    # Copiar package.json puede ser necesario si tienes scripts de composer que usen npm
    COPY package.json package-lock.json ./
    RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader
    
    
    # --- Build Stage: Frontend Assets (Node + Vite) ---
    # Usar una imagen de Node que coincida con tu versión local si es posible
    FROM node:18 as frontend
    
    WORKDIR /app
    COPY --from=vendor /app/vendor/ /app/vendor/ # Copiar vendor para scripts post-install/
    COPY package*.json ./
    RUN npm install
    COPY . .
    RUN npm run build
    
    
    # --- Final Stage: Production Image (PHP-FPM + Nginx) ---
    # Usar una imagen oficial de PHP con FPM y Alpine para tamaño reducido
    FROM php:8.2-fpm-alpine as production
    
    # Instalar dependencias PHP necesarias y Nginx
    # Ajusta las extensiones PHP según las necesidades EXACTAS de tu proyecto
    RUN apk add --no-cache \
            nginx \
            supervisor \
            # Extensiones PHP comunes para Laravel:
            php82-fpm \
            php82-pdo \
            php82-pdo_mysql \
            php82-pdo_pgsql \
            php82-pdo_sqlite \
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
            # Opcional: para cache/colas si las usas
            # php82-redis \
            # php82-pcntl \
            ;
    
    WORKDIR /var/www/html
    
    # Copiar archivos de configuración (los crearemos en el siguiente paso)
    COPY docker/nginx.conf /etc/nginx/nginx.conf
    COPY docker/supervisord.conf /etc/supervisord.conf
    COPY docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
    
    # Copiar código de la aplicación (vendor desde la etapa 'vendor', assets desde 'frontend')
    COPY --from=vendor /app/vendor/ /var/www/html/vendor/
    COPY --from=frontend /app/public/build/ /var/www/html/public/build/
    COPY --from=frontend /app/public/index.php /var/www/html/public/index.php
    # Copiar el resto del código de la app
    COPY . /var/www/html
    
    # Establecer permisos correctos (importante para logs, cache, etc.)
    RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
        && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
    
    # Exponer puerto 80 (Nginx escuchará aquí)
    EXPOSE 80
    
    # Comando para iniciar Supervisor (que iniciará Nginx y PHP-FPM)
    CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]