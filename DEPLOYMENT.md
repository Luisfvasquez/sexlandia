# SEXLANDIA — Runbook de despliegue en VPS

Guía paso por paso para poner la aplicación en producción sobre un VPS limpio.
Pensada para ejecutarse de forma secuencial (por una persona o por un agente de
Claude Code con acceso `ssh` al servidor).

- **Stack objetivo:** Ubuntu 24.04 LTS · PHP 8.4 · MySQL 8 · Nginx · Redis · Node 20 (solo build)
- **Recursos mínimos:** 2 vCPU / 4 GB RAM / 40 GB SSD
- Sustituye todos los valores entre `<...>` antes de ejecutar.
- Marca cada bloque como completado antes de pasar al siguiente.

---

## 0. Variables de la sesión

Define estas variables una vez (ajusta los valores) y reusa en los comandos siguientes:

```bash
DOMAIN="<tudominio.com>"
APP_DIR="/var/www/sexlandia"
REPO_URL="<git@github.com:usuario/sexlandia.git>"
DB_NAME="sexlandia"
DB_USER="sexlandia"
DB_PASS="<contraseña-BD-fuerte>"
DEPLOY_USER="deploy"
```

---

## 1. Base del sistema

```bash
sudo apt update && sudo apt -y upgrade
sudo apt -y install software-properties-common curl git unzip acl ufw fail2ban

# PHP 8.4 (repositorio ondrej)
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt -y install \
  php8.4-fpm php8.4-cli php8.4-common \
  php8.4-mysql php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip \
  php8.4-bcmath php8.4-gd php8.4-intl php8.4-redis php8.4-opcache

php -v   # debe decir 8.4.x
```

Ajustes de PHP-FPM (`/etc/php/8.4/fpm/php.ini`):

```bash
sudo sed -i 's/^;*upload_max_filesize.*/upload_max_filesize = 12M/' /etc/php/8.4/fpm/php.ini
sudo sed -i 's/^;*post_max_size.*/post_max_size = 14M/'            /etc/php/8.4/fpm/php.ini
sudo sed -i 's/^;*memory_limit.*/memory_limit = 256M/'            /etc/php/8.4/fpm/php.ini
sudo sed -i 's/^;*opcache.enable=.*/opcache.enable=1/'            /etc/php/8.4/mods-available/opcache.ini
sudo systemctl restart php8.4-fpm
```

---

## 2. MySQL

```bash
sudo apt -y install mysql-server
sudo mysql_secure_installation   # define root pass, elimina anónimos/test

sudo mysql -e "CREATE DATABASE ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';"
sudo mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'127.0.0.1';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

Confirma que MySQL solo escucha en local:

```bash
grep -E '^bind-address' /etc/mysql/mysql.conf.d/mysqld.cnf   # 127.0.0.1
```

---

## 3. Redis

```bash
sudo apt -y install redis-server
sudo sed -i 's/^# *maxmemory .*/maxmemory 256mb/' /etc/redis/redis.conf
sudo sed -i 's/^# *maxmemory-policy .*/maxmemory-policy allkeys-lru/' /etc/redis/redis.conf
sudo systemctl enable --now redis-server
redis-cli ping   # PONG
```

---

## 4. Nginx

```bash
sudo apt -y install nginx
sudo systemctl enable --now nginx
```

---

## 5. Composer y Node

```bash
# Composer 2
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version

# Node 20 (solo para 'npm run build')
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt -y install nodejs
node -v
```

---

## 6. Usuario de despliegue y código

```bash
sudo adduser --disabled-password --gecos "" ${DEPLOY_USER}
sudo usermod -aG www-data ${DEPLOY_USER}
sudo mkdir -p ${APP_DIR}
sudo chown ${DEPLOY_USER}:www-data ${APP_DIR}

sudo -u ${DEPLOY_USER} git clone ${REPO_URL} ${APP_DIR}
cd ${APP_DIR}
sudo -u ${DEPLOY_USER} git checkout main   # o la rama de release
```

---

## 7. Archivo `.env` de producción

```bash
cd ${APP_DIR}
sudo -u ${DEPLOY_USER} cp .env.example .env
sudo -u ${DEPLOY_USER} nano .env
```

Valores obligatorios para producción:

```dotenv
APP_NAME=SEXLANDIA
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<tudominio.com>
SITE_URL=https://<tudominio.com>

APP_LOCALE=es
APP_FALLBACK_LOCALE=en
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sexlandia
DB_USERNAME=sexlandia
DB_PASSWORD=<contraseña-BD-fuerte>

# Redis para sesión/caché/colas
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null

SESSION_SECURE_COOKIE=true
SESSION_LIFETIME=120

FILESYSTEM_DISK=local

# Broadcasting en tiempo real: 'log' si NO usarás WebSockets en el MVP.
BROADCAST_CONNECTION=log
# Si activas Reverb, pon BROADCAST_CONNECTION=reverb y completa:
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST="<tudominio.com>"
REVERB_PORT=443
REVERB_SCHEME=https

# Correo real (ejemplo con SMTP)
MAIL_MAILER=smtp
MAIL_HOST=<smtp-host>
MAIL_PORT=587
MAIL_USERNAME=<smtp-user>
MAIL_PASSWORD=<smtp-pass>
MAIL_FROM_ADDRESS="no-responder@<tudominio.com>"
MAIL_FROM_NAME="SEXLANDIA"

# --- Credenciales del seeder (OBLIGATORIO definirlas en producción) ---
SEED_ADMIN_EMAIL=<tu-email-admin>
SEED_ADMIN_PASSWORD=<contraseña-admin-fuerte>
SEED_ADMIN_NAME="Administrador"
SEED_ADMIN_LASTNAME="SEXLANDIA"
SEED_ADMIN_DNI=<cedula>
SEED_ADMIN_PHONE=<telefono>

# Catálogo demo: true SOLO en el primer despliegue, luego cámbialo a false.
SEED_DEMO_DATA=true

# Retención de la tabla de auditoría (meses)
AUDIT_RETENTION_MONTHS=6
```

---

## 8. Instalación de la aplicación

```bash
cd ${APP_DIR}

sudo -u ${DEPLOY_USER} composer install --no-dev --optimize-autoloader --no-interaction
sudo -u ${DEPLOY_USER} npm ci
sudo -u ${DEPLOY_USER} npm run build

sudo -u ${DEPLOY_USER} php artisan key:generate --force
sudo -u ${DEPLOY_USER} php artisan storage:link
sudo -u ${DEPLOY_USER} php artisan migrate --force

# Primer despliegue: siembra estructura + admin + catálogo demo.
# Guarda la contraseña de admin que imprime si no definiste SEED_ADMIN_PASSWORD.
sudo -u ${DEPLOY_USER} php artisan db:seed --force

# Cachés de producción
sudo -u ${DEPLOY_USER} php artisan config:cache
sudo -u ${DEPLOY_USER} php artisan route:cache
sudo -u ${DEPLOY_USER} php artisan view:cache
sudo -u ${DEPLOY_USER} php artisan event:cache
```

> Tras el primer despliegue, edita `.env` y pon `SEED_DEMO_DATA=false` para que
> `db:seed` no vuelva a borrar el catálogo real.

---

## 9. Permisos de archivos

```bash
cd ${APP_DIR}
sudo chown -R ${DEPLOY_USER}:www-data .
sudo find . -type f -exec chmod 644 {} \;
sudo find . -type d -exec chmod 755 {} \;
sudo chmod -R ug+rwX storage bootstrap/cache
sudo setfacl -R -m u:www-data:rwX -m d:u:www-data:rwX storage bootstrap/cache
sudo chmod 640 .env
```

---

## 10. Nginx: virtual host

```bash
sudo tee /etc/nginx/sites-available/sexlandia >/dev/null <<'NGINX'
server {
    listen 80;
    server_name DOMAIN_PLACEHOLDER;
    root /var/www/sexlandia/public;
    index index.php;

    charset utf-8;
    client_max_body_size 14M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Bloquea dotfiles (.env, .git, ...) pero deja pasar .well-known
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # WebSockets de Reverb (solo si BROADCAST_CONNECTION=reverb)
    location /app/ {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
NGINX

sudo sed -i "s/DOMAIN_PLACEHOLDER/${DOMAIN}/" /etc/nginx/sites-available/sexlandia
sudo ln -sf /etc/nginx/sites-available/sexlandia /etc/nginx/sites-enabled/sexlandia
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx
```

---

## 11. HTTPS (Let's Encrypt)

```bash
sudo apt -y install certbot python3-certbot-nginx
sudo certbot --nginx -d ${DOMAIN} -d www.${DOMAIN} --redirect --agree-tos -m admin@${DOMAIN} --no-eff-email
sudo systemctl status certbot.timer   # renovación automática
```

Añade cabeceras de seguridad al bloque `server` HTTPS que generó certbot
(`/etc/nginx/sites-available/sexlandia`):

```nginx
add_header X-Content-Type-Options "nosniff" always;
add_header X-Frame-Options "SAMEORIGIN" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
```

```bash
sudo nginx -t && sudo systemctl reload nginx
```

---

## 12. Procesos en segundo plano (systemd + cron)

### 12.1 Worker de colas

```bash
sudo tee /etc/systemd/system/sexlandia-worker.service >/dev/null <<'UNIT'
[Unit]
Description=SEXLANDIA queue worker
After=network.target mysql.service redis-server.service

[Service]
User=deploy
Group=www-data
Restart=always
RestartSec=5
WorkingDirectory=/var/www/sexlandia
ExecStart=/usr/bin/php /var/www/sexlandia/artisan queue:work --sleep=1 --tries=3 --max-time=3600 --timeout=90

[Install]
WantedBy=multi-user.target
UNIT

sudo systemctl daemon-reload
sudo systemctl enable --now sexlandia-worker.service
sudo systemctl status sexlandia-worker.service
```

### 12.2 Scheduler (tasa Bs/USD + poda de auditoría)

```bash
sudo crontab -u deploy -l 2>/dev/null | { cat; echo "* * * * * cd /var/www/sexlandia && php artisan schedule:run >> /dev/null 2>&1"; } | sudo crontab -u deploy -
sudo crontab -u deploy -l
```

### 12.3 Reverb (SOLO si usas WebSockets — omite si `BROADCAST_CONNECTION=log`)

```bash
cd /var/www/sexlandia
sudo -u deploy php artisan reverb:install   # genera claves REVERB_* en .env
# copia REVERB_APP_KEY/HOST/PORT/SCHEME a las VITE_REVERB_* del .env y reconstruye:
sudo -u deploy npm run build
sudo -u deploy php artisan config:cache

sudo tee /etc/systemd/system/sexlandia-reverb.service >/dev/null <<'UNIT'
[Unit]
Description=SEXLANDIA Reverb WebSocket server
After=network.target

[Service]
User=deploy
Group=www-data
Restart=always
RestartSec=5
WorkingDirectory=/var/www/sexlandia
ExecStart=/usr/bin/php /var/www/sexlandia/artisan reverb:start --host=127.0.0.1 --port=8080

[Install]
WantedBy=multi-user.target
UNIT

sudo systemctl daemon-reload
sudo systemctl enable --now sexlandia-reverb.service
```

---

## 13. Firewall

```bash
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
sudo ufw status verbose
# NO abrir 3306 (MySQL), 6379 (Redis) ni 8080 (Reverb interno).
```

Endurecer SSH (`/etc/ssh/sshd_config`): `PermitRootLogin no`, `PasswordAuthentication no`
(asegúrate antes de tener tu clave pública en `~/.ssh/authorized_keys`), luego
`sudo systemctl restart ssh`.

---

## 14. Verificación post-despliegue

```bash
cd /var/www/sexlandia

sudo -u deploy php artisan about
curl -sI https://${DOMAIN}/up            # 200
curl -sI https://${DOMAIN}/              # 200, portada
curl -sI https://${DOMAIN}/sitemap.xml   # 200
sudo -u deploy php artisan queue:work --once   # procesa un job de prueba y termina
sudo systemctl is-active sexlandia-worker nginx php8.4-fpm mysql redis-server
sudo -u deploy php artisan schedule:list
```

Checklist manual:

- [ ] Login en `/login` con el admin sembrado → entra al panel.
- [ ] La portada pública carga con productos e imágenes.
- [ ] Crear un pedido de prueba desde el checkout; el comprobante se ve en
      `admin/orders/{id}` (se sirve por ruta autenticada, NO por `/storage`).
- [ ] `APP_DEBUG=false` (una URL inexistente muestra 404 genérico, no traza).
- [ ] `SEED_DEMO_DATA=false` en `.env` tras cargar el catálogo inicial.

---

## 15. Redespliegue (actualizaciones)

```bash
cd /var/www/sexlandia
sudo -u deploy php artisan down --render="errors::503"

sudo -u deploy git pull --ff-only
sudo -u deploy composer install --no-dev --optimize-autoloader --no-interaction
sudo -u deploy npm ci && sudo -u deploy npm run build
sudo -u deploy php artisan migrate --force

sudo -u deploy php artisan optimize:clear
sudo -u deploy php artisan config:cache
sudo -u deploy php artisan route:cache
sudo -u deploy php artisan view:cache
sudo -u deploy php artisan event:cache

sudo systemctl restart sexlandia-worker.service
# sudo systemctl restart sexlandia-reverb.service   # si aplica
sudo -u deploy php artisan up
```

> No ejecutes `php artisan db:seed` en redespliegues salvo que necesites sembrar
> nuevos permisos: `php artisan db:seed --class=RolesAndPermissionsSeeder --force`.

---

## 16. Copias de seguridad

```bash
sudo tee /usr/local/bin/sexlandia-backup.sh >/dev/null <<'SH'
#!/bin/bash
set -e
STAMP=$(date +%F_%H%M)
DEST=/var/backups/sexlandia
mkdir -p "$DEST"
mysqldump --single-transaction --user=sexlandia --password="<DB_PASS>" sexlandia | gzip > "$DEST/db_$STAMP.sql.gz"
tar czf "$DEST/storage_$STAMP.tar.gz" -C /var/www/sexlandia storage/app
find "$DEST" -type f -mtime +14 -delete
SH
sudo chmod +x /usr/local/bin/sexlandia-backup.sh
echo "30 3 * * * root /usr/local/bin/sexlandia-backup.sh" | sudo tee /etc/cron.d/sexlandia-backup
```

Sube `db_*.sql.gz` y `storage_*.tar.gz` a almacenamiento externo (S3/Backblaze).
`storage/app/private/receipts` contiene comprobantes de pago de clientes: trátalo
como dato sensible (cifrado en reposo si es posible, acceso restringido).

---

## Notas de arquitectura relevantes para el despliegue

- **Comprobantes de pago** se guardan en el disco privado `local`
  (`storage/app/private/receipts`) y se sirven solo por la ruta autenticada
  `admin.orders.proofImage` (grupo `role:admin`). Nunca son accesibles por URL
  pública.
- **Precios y transacciones** se almacenan en USD; el equivalente en Bs se deriva
  con la tasa activa (`CurrencyService`). El comando `exchange:update-usd` la
  actualiza 2×/día (requiere salida a Internet).
- **Roles activos:** `admin`, `client`, `delivery`. Los roles `seller` y
  `warehouse` existen en la gestión de roles pero sus rutas siguen protegidas por
  `role:admin` hasta habilitar la fase de permisos granulares (el rol `admin`
  ya actúa como superusuario vía `Gate::before`).
- **Zona horaria:** la app corre en `UTC`; las tareas programadas fijan
  `America/Caracas` explícitamente.
- La tabla `audits` se poda semanalmente (`audits:prune`, retención
  `AUDIT_RETENTION_MONTHS`).
