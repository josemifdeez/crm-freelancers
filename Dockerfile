# Dockerfile optimizado para Laravel + Vite en Render (v4 - Fix Supervisor Logs)

# --- Build Stage: PHP Dependencies (Composer) ---
    FROM composer:2 as vendor

    WORKDIR /app
    COPY database/ database/
    COPY composer.json composer.lock ./
    COPY package.json package-lock.json ./
    
    # Instalar dependencias SIN ejecutar scripts aquí
    RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader --no-scripts
    
    
    # --- Build Stage: Frontend Assets (Node + Vite) ---
    FROM node:18 as frontend 
    
    WORKDIR /app
    COPY --from=vendor /app/vendor/ /app/vendor/
    COPY package*.json ./
    RUN npm install
    # Copiar todo el código para contexto de build de Vite
    COPY . .
    RUN npm run build
    
    
    # --- Final Stage: Production Image (PHP-FPM + Nginx) ---
    FROM php:8.2-fpm-alpine as production
    
    # --- Copiar Composer desde la etapa 'vendor' ---
    COPY --from=composer /usr/bin/composer /usr/local/bin/composer
    
    # Instalar dependencias PHP y Nginx
    RUN apk add --no-cache \
            nginx \
            supervisor \
            php82-fpm \
            php82-pdo \
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
            # Opcional: php82-redis, php82-pcntl
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
    
    # Copiar el resto del código de la aplicación
    COPY . /var/www/html/
    
    # Ejecutar SOLO composer dump-autoload aquí
    RUN composer dump-autoload --optimize --no-dev --classmap-authoritative
    # Los comandos de Artisan se ejecutarán manualmente o vía pre-deploy/start script
    
    # Establecer permisos correctos
    # Nota: Crear storage/framework subdirs aquí si no existen en Git es buena práctica
    RUN mkdir -p /var/www/html/storage/framework/sessions \
                 /var/www/html/storage/framework/views \
                 /var/www/html/storage/framework/cache/data \
                 /var/www/html/storage/logs \
        && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
        && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
    
    # --- Crear directorios para logs y PID de Supervisor ---
    # También crear /var/run para el PID si no existe
    RUN mkdir -p /var/log/supervisor /var/run \
        && chown -R www-data:www-data /var/log/supervisor /var/run \
        && chmod -R 775 /var/log/supervisor /var/run
    
    EXPOSE 80
    
    # Comando final para iniciar Supervisor directamente
    # (Recuerda ejecutar 'php artisan optimize:clear', 'config:cache', etc., manualmente en la Shell de Render)
    CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
    
    # Si prefieres el script de inicio (Alternativa 1):
    # COPY docker/start.sh /usr/local/bin/start-app
    # RUN chmod +x /usr/local/bin/start-app
    # CMD ["start-app"]