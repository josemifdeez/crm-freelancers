#!/bin/sh
set -e # Salir inmediatamente si un comando falla

echo "Running Laravel setup commands..."

# Ejecutar comandos de optimización y enlace
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link

# --- MIGRACIONES PARA POSTGRESQL (ASEGURAR QUE ESTÉ ACTIVO) ---
echo "Running database migrations..."
php artisan migrate --force --seed # Descomentado. Añade --seed si tienes seeders y quieres ejecutarlos.
# -----------------------------------------------------------

echo "Starting Supervisor..."
# Usar 'exec' para reemplazar el proceso del script con supervisord
exec /usr/bin/supervisord -c /etc/supervisord.conf