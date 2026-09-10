#!/usr/bin/env bash
# ==============================================================================
# Script de Despliegue Automático (deploy.sh) - SEXLANDIA
# ==============================================================================
set -e

BRANCH="${1:-main}"
PROJECT_DIR="/var/www/sexlandia"

echo "---------------------------------------------------------"
echo "🚀 Iniciando despliegue de SEXLANDIA (Rama: $BRANCH)..."
echo "🕒 $(date '+%Y-%m-%d %H:%M:%S')"
echo "---------------------------------------------------------"

cd "$PROJECT_DIR"

# 1. Poner en mantenimiento
echo "🛑 Activando modo mantenimiento..."
php artisan down --render="errors::503" || true

# 2. Descargar últimos cambios
echo "📥 Obteniendo cambios desde git ($BRANCH)..."
git fetch origin "$BRANCH"
git checkout "$BRANCH"
git pull --ff-only origin "$BRANCH"

# 3. Dependencias de PHP y Frontend
echo "📦 Instalando dependencias de Composer..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "🎨 Compilando assets de frontend con Vite..."
npm ci --prefer-offline --no-audit
npm run build

# 4. Migraciones
echo "🗄️  Ejecutando migraciones de base de datos..."
php artisan migrate --force

# 5. Cachés de Laravel
echo "⚡ Optimizando cachés de Laravel..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Reiniciar Worker y servicios
echo "🔄 Reiniciando worker de colas (Queue Worker)..."
if systemctl is-active --quiet sexlandia-worker.service; then
    sudo systemctl restart sexlandia-worker.service
else
    php artisan queue:restart
fi

# Reiniciar Reverb WebSockets si el servicio existe
if systemctl list-unit-files | grep -q "sexlandia-reverb.service"; then
    echo "📡 Reiniciando servicio de WebSockets (Reverb)..."
    sudo systemctl restart sexlandia-reverb.service || true
fi

# 7. Levantar la aplicación
echo "🟢 Desactivando modo mantenimiento..."
php artisan up

echo "---------------------------------------------------------"
echo "✅ ¡Despliegue completado con éxito!"
echo "---------------------------------------------------------"