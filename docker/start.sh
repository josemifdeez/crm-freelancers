#!/bin/sh
set -e # Salir inmediatamente si un comando falla

echo "Running Laravel setup commands..."

# Ejecutar comandos de optimización y enlace
# Asegúrate que la aplicación pueda escribir en storage/logs si usas Log::
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link

# OJO con las migraciones en SQLite - Comentado por defecto
# ya que el archivo .sqlite subido con Git ya debería estar migrado.
# Descomenta solo si inicias con una base de datos externa y vacía.
# echo "Running database migrations..."
# php artisan migrate --force --seed

echo "Starting Supervisor..."
# Usar 'exec' para reemplazar el proceso del script con supervisord
exec /usr/bin/supervisord -c /etc/supervisord.conf