#!/bin/sh
set -e # Salir inmediatamente si un comando falla

# --- Ejecutar migraciones ANTES de cualquier optimización ---
echo "Running database migrations FIRST..."
# Asumiendo que tienes las migraciones _create_sessions_table y _create_cache_table
php artisan migrate --force --seed # Añade --seed si tienes seeders y quieres ejecutarlos

# --- Ejecutar optimizaciones DESPUÉS de migrar ---
echo "Running Laravel optimization commands..."
php artisan optimize:clear # Ahora debería encontrar la tabla 'cache' y 'sessions' si los drivers las usan
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link # Es importante ejecutar storage:link después de que la estructura de carpetas esté estable

echo "Starting Supervisor..."
# Usar 'exec' para reemplazar el proceso del script con supervisord
exec /usr/bin/supervisord -c /etc/supervisord.conf