# Dockerfile optimizado para Laravel + Vite en Render

# --- Build Stage: PHP Dependencies (Composer) ---
    FROM composer:2 as vendor

    WORKDIR /app
    COPY database/ database/
    COPY composer.json composer.lock ./
    # Copiar package.json por si scripts de composer lo usan
    COPY package.json package-lock.json ./
    
    # Instalar dependencias SIN ejecutar scripts aquí
    RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader --no-scripts
    
    
    # --- Build Stage: Frontend Assets (Node + Vite) ---
    # Usar una imagen de Node que coincida con tu versión local si es posible
    FROM node:18 as frontend
    
    WORKDIR /app
    COPY --from=vendor /app/vendor/ /app/vendor/ 
    COPY package*.json ./
    RUN npm install
    # Copiar todo el código para que Vite tenga acceso a todo (ej. tailwind.config.js)
    COPY . .
    RUN npm run build
    
    
    # --- Final Stage: Production Image (PHP-FPM + Nginx) ---
    # Usar una imagen oficial de PHP con FPM y Alpine para tamaño reducido
    FROM php:8.2-fpm-alpine as production
    
    # Instalar dependencias PHP necesarias y Nginx
    # Revisa si realmente necesitas todas estas extensiones
    RUN apk add --no-cache \
            nginx \
            supervisor \
            # Extensiones PHP comunes para Laravel:
            php82-fpm \
            php82-pdo \
            # php82-pdo_mysql \ # Comenta si no usas MySQL
            # php82-pdo_pgsql \ # Comenta si no usas PostgreSQL # 
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
    
    # Copiar archivos de configuración de Docker/Nginx/PHP-FPM/Supervisor
    COPY docker/nginx.conf /etc/nginx/nginx.conf
    COPY docker/supervisord.conf /etc/supervisord.conf
    COPY docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
    
    # Copiar artefactos de las etapas de build
    COPY --from=vendor /app/vendor/ /var/www/html/vendor/
    COPY --from=frontend /app/public/build/ /var/www/html/public/build/
    COPY --from=frontend /app/public/index.php /var/www/html/public/index.php
    
    # Copiar el resto del código de la aplicación (asegúrate de que el destino termine en /)
    COPY . /var/www/html/
    
    # Ejecutar scripts de Composer AHORA que todo el código está presente
    RUN composer dump-autoload --optimize --no-dev --classmap-authoritative && \
        php artisan optimize:clear && \
        php artisan package:discover --ansi
        # Considera añadir aquí también php artisan config:cache y php artisan route:cache
        # aunque a veces es mejor hacerlo en el Pre-Deploy de Render si no usas Docker entrypoint
    
    # Establecer permisos correctos
    RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
        && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
    
    # Exponer puerto 80
    EXPOSE 80
    
    # Comando final para iniciar Supervisor
    CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]