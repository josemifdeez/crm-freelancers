# Dockerfile optimizado para Laravel + Vite en Render (v8 - Usando docker-php-ext-install)

# --- Build Stage: PHP Dependencies (Composer) ---
# ... (Sin cambios) ...
FROM composer:2 as vendor
WORKDIR /app
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader --no-scripts

# --- Build Stage: Frontend Assets (Node + Vite) ---
# ... (Sin cambios) ...
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

# --- Instalar dependencias (Revisado para pdo_pgsql usando ext-install) ---
# Necesitamos 'build-base' y 'postgresql-dev' para compilar la extensión
RUN apk add --no-cache \
        nginx \
        supervisor \
        build-base \ 
        postgresql-dev \ 
        # Paquetes PHP base y otras extensiones (instaladas con apk si es posible)
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
        # Comentar/eliminar otras extensiones PDO si no se usan
        # php82-pdo_sqlite \
        # php82-pdo_mysql \
        ;

# --- Instalar extensión pdo_pgsql usando el helper de Docker PHP ---
RUN docker-php-ext-install pdo_pgsql

# Limpiar paquetes de build después de instalar la extensión para reducir tamaño
RUN apk del build-base postgresql-dev

# --- Fin de la sección de dependencias ---


WORKDIR /var/www/html

# Copiar archivos de configuración (sin cambios)
COPY docker/nginx.conf /etc/nginx/nginx.conf
# ... (resto de COPY de config) ...

# Copiar artefactos y código (sin cambios)
COPY --from=vendor /app/vendor/ /var/www/html/vendor/
# ... (resto de COPY de artefactos y código) ...
COPY . /var/www/html/

# Ejecutar SOLO composer dump-autoload aquí
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# Crear directorios y establecer permisos (sin cambios)
RUN mkdir -p /var/www/html/storage/framework/sessions \
# ... (resto de mkdir y chown/chmod) ...
RUN mkdir -p /var/log/supervisor /var/run \
# ... (resto de chown/chmod para supervisor) ...

# Copiar y hacer ejecutable el script de inicio (sin cambios)
COPY docker/start.sh /usr/local/bin/start-app
RUN chmod +x /usr/local/bin/start-app

EXPOSE 80

# CMD para usar el script de inicio (sin cambios)
CMD ["start-app"]