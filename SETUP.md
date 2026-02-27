# Kromerce - Guía de Configuración y Montaje

## Requisitos del Sistema

### Versiones Requeridas
- **PHP**: 8.2+ (probado con 8.3.30)
- **Node.js**: 20.x (probado con 20.19.4)
- **NPM**: 10.x (probado con 10.8.2)
- **Composer**: 2.x
- **Base de datos**: MySQL 8.0+ o PostgreSQL 13+

### Extensiones PHP Requeridas
- PDO
- PDO_MySQL o PDO_PostgreSQL
- MBString
- BCMath
- OPcache
- Zip
- XML

---

## Configuración Rápida (Local Development)

### 1. Clonar el Repositorio
```bash
git clone <repository-url>
cd kromerce
```

### 2. Configurar Variables de Entorno
```bash
cp .env.example .env
```

### 3. Instalar Dependencias
```bash
# Dependencias PHP
composer install

# Dependencias Node.js
npm install
```

### 4. Generar Key de Laravel
```bash
php artisan key:generate
```

### 5. Configurar Base de Datos

#### Opción A: MySQL
```bash
# Editar .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kromerce
DB_USERNAME=root
DB_PASSWORD=tu_password
```

#### Opción B: PostgreSQL
```bash
# Editar .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=kromerce
DB_USERNAME=postgres
DB_PASSWORD=tu_password
```

#### Opción C: SQLite (para desarrollo rápido)
```bash
# Crear base de datos SQLite
touch database/database.sqlite

# Editar .env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

### 6. Migrar Base de Datos
```bash
php artisan migrate
```

### 7. Sembrar Base de Datos (Opcional)
```bash
php artisan db:seed
```

### 8. Construir Assets
```bash
npm run build
```

### 9. Iniciar Servidor de Desarrollo
```bash
# Usando el script de composer
composer run dev

# O manualmente
php artisan serve
npm run dev
```

---

## Configuración con Docker (Producción)

### 1. Construir Imagen Docker
```bash
docker build -t kromerce .
```

### 2. Ejecutar Contenedor
```bash
# Con MySQL
docker run -d \
  --name kromerce \
  -p 80:80 \
  -e DB_CONNECTION=mysql \
  -e DB_HOST=mysql_host \
  -e DB_DATABASE=kromerce \
  -e DB_USERNAME=root \
  -e DB_PASSWORD=password \
  kromerce

# Con PostgreSQL
docker run -d \
  --name kromerce \
  -p 80:80 \
  -e DB_CONNECTION=pgsql \
  -e DB_HOST=postgres_host \
  -e DB_DATABASE=kromerce \
  -e DB_USERNAME=postgres \
  -e DB_PASSWORD=password \
  kromerce
```

### 3. Configuración Local con Docker

Para desarrollo local usando Docker:

```bash
# Usar docker-compose.dev.yml para desarrollo local
docker-compose -f docker-compose.dev.yml up -d app mysql redis

# Con PostgreSQL
docker-compose -f docker-compose.dev.yml --profile postgres up -d app postgres redis

# Acceder a la aplicación
# http://localhost:8000
```

**Nota**: `docker-compose.dev.yml` está configurado específicamente para desarrollo local con:
- Base de datos separada (`kromerce_dev`)
- Puertos diferentes para no conflictuar
- Volúmenes de código para hot-reload
- `APP_ENV=local` y `APP_DEBUG=true`

### 4. Configuración Manual (Sin Docker)

Si prefieres no usar Docker, sigue la configuración rápida anterior.

---

## Configuración de Nginx

El archivo `nginx.conf` está configurado para producción con:

- Compresión gzip activada
- Proxy para Vite HMR (desarrollo)
- Manejo de assets estáticos
- Configuración PWA
- Headers de seguridad
- Soporte para PHP-FPM

### Configuración en Producción
```bash
# Copiar configuración de nginx
sudo cp nginx.conf /etc/nginx/sites-available/kromerce
sudo ln -s /etc/nginx/sites-available/kromerce /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Configuración en WSL2/Linux

#### 1. Instalar Nginx
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install nginx

# CentOS/RHEL
sudo yum install nginx
# o
sudo dnf install nginx
```

#### 2. Configurar Nginx para Kromerce
```bash
# Copiar configuración del proyecto
sudo cp /ruta/a/kromerce/nginx.conf /etc/nginx/sites-available/kromerce

# Habilitar el sitio
sudo ln -s /etc/nginx/sites-available/kromerce /etc/nginx/sites-enabled/

# Deshabilitar sitio default (opcional)
sudo rm /etc/nginx/sites-enabled/default
```

#### 3. Configurar PHP-FPM
```bash
# Instalar PHP-FPM si no está instalado
sudo apt install php8.3-fpm

# Iniciar y habilitar PHP-FPM
sudo systemctl start php8.3-fpm
sudo systemctl enable php8.3-fpm

# Verificar que está corriendo
sudo systemctl status php8.3-fpm
```

#### 4. Configurar Permisos
```bash
# Crear directorios necesarios
sudo mkdir -p /var/www/html/storage/logs
sudo mkdir -p /var/www/html/storage/framework/cache
sudo mkdir -p /var/www/html/storage/framework/sessions
sudo mkdir -p /var/www/html/storage/framework/views

# Configurar permisos
sudo chown -R www-data:www-data /var/www/html/storage
sudo chown -R www-data:www-data /var/www/html/bootstrap/cache
sudo chmod -R 775 /var/www/html/storage
sudo chmod -R 775 /var/www/html/bootstrap/cache
```

#### 5. Configurar Hosts Locales (Opcional)
```bash
# Editar /etc/hosts
sudo nano /etc/hosts

# Agregar línea para dominio local
127.0.0.1 kromerce.local
```

#### 6. Configurar SSL con Certbot (Opcional)
```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx

# Obtener certificado SSL
sudo certbot --nginx -d kromerce.local

# O para desarrollo local con certificado auto-firmado
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/ssl/private/kromerce-selfsigned.key \
  -out /etc/ssl/certs/kromerce-selfsigned.crt
```

#### 7. Iniciar Servicios
```bash
# Iniciar Nginx
sudo systemctl start nginx
sudo systemctl enable nginx

# Verificar configuración
sudo nginx -t

# Recargar configuración
sudo systemctl reload nginx
```

### Configuración para Entornos Virtualizados

#### WSL2 con Windows Host
```bash
# En WSL2, obtener IP del host
ip route show | grep default

# Configurar nginx.conf para permitir conexión desde Windows
# Agregar en la sección http:
server {
    listen 80;
    listen [::]:80;
    server_name _;
    # ... configuración existente
}

# Acceder desde Windows usando:
# http://localhost:80 o http://<IP_WSL2>:80
```

#### Docker con Nginx Externo
```bash
# Usar perfil nginx-external en docker-compose.yml
docker-compose --profile nginx-external up -d

# Configurar nginx.conf en host para proxy al contenedor
upstream kromerce_app {
    server app:9000;
}

server {
    listen 80;
    server_name kromerce.local;
    
    location / {
        proxy_pass http://kromerce_app;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### Troubleshooting de Nginx

#### Problemas Comunes
```bash
# Verificar estado de nginx
sudo systemctl status nginx

# Ver logs de nginx
sudo journalctl -u nginx
sudo tail -f /var/log/nginx/error.log

# Verificar configuración
sudo nginx -t

# Verificar conexión PHP-FPM
sudo netstat -tlnp | grep :9000
```

#### Errores Frecuentes

1. **502 Bad Gateway**
   - PHP-FPM no está corriendo
   - Configuración incorrecta del socket

2. **403 Forbidden**
   - Permisos incorrectos en archivos
   - Configuración de index incorrecta

3. **504 Gateway Timeout**
   - Tiempo de espera agotado
   - Configurar `fastcgi_read_timeout`

#### Configuración Adicional para Desarrollo
```nginx
# En nginx.conf, agregar para desarrollo
location /@vite/ {
    proxy_pass http://127.0.0.1:5173/@vite/;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
    proxy_cache_bypass $http_upgrade;
}

# Headers CORS para desarrollo
add_header Access-Control-Allow-Origin "*";
add_header Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS";
add_header Access-Control-Allow-Headers "Content-Type, Authorization";
```

---

## Scripts Automatizados

### Scripts de Configuración

El proyecto incluye scripts automatizados para facilitar la configuración:

#### Configuración General de Nginx
```bash
# Ejecutar script de configuración (requiere sudo)
sudo bash scripts/setup-nginx.sh
```

#### Configuración Específica para WSL2
```bash
# Script optimizado para WSL2 con Windows host
bash scripts/setup-nginx-wsl.sh
```

Estos scripts realizan automáticamente:
- Instalación de Nginx y PHP-FPM
- Configuración del virtual host
- Configuración de permisos
- Inicio de servicios
- Verificación de configuración
- Instrucciones específicas para WSL2

---

## Scripts Útiles

### Composer Scripts Disponibles
```bash
# Instalación completa
composer run setup

# Desarrollo (todos los servicios)
composer run dev

# Testing
composer run test
```

### Scripts NPM
```bash
# Desarrollo
npm run dev

# Desarrollo forzado (host 0.0.0.0)
npm run dev:force

# Build para producción
npm run build

# Preview del build
npm run preview
```

---

## Configuración de Base de Datos Detallada

### MySQL
```sql
-- Crear base de datos
CREATE DATABASE kromerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario (opcional)
CREATE USER 'kromerce'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON kromerce.* TO 'kromerce'@'localhost';
FLUSH PRIVILEGES;
```

### PostgreSQL
```sql
-- Crear base de datos
CREATE DATABASE kromerce;

-- Crear usuario (opcional)
CREATE USER kromerce WITH PASSWORD 'password';
GRANT ALL PRIVILEGES ON DATABASE kromerce TO kromerce;
```

---

## Variables de Entorno Importantes

### Configuración Básica
```env
APP_NAME=Kromerce
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
```

### Configuración de Base de Datos
```env
# MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kromerce
DB_USERNAME=root
DB_PASSWORD=

# PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=kromerce
DB_USERNAME=postgres
DB_PASSWORD=
```

### Configuración Adicional
```env
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

---

## Troubleshooting

### Problemas Comunes

1. **Error de conexión a base de datos**
   - Verificar que el servicio de BD esté corriendo
   - Confirmar credenciales en `.env`
   - Revisar que la base de datos exista

2. **Error de permisos**
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

3. **Error de dependencias Node.js**
   ```bash
   rm -rf node_modules package-lock.json
   npm install
   ```

4. **Error de dependencias PHP**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

### Limpiar Caché
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## Flujo de Trabajo de Desarrollo

1. **Crear nueva feature branch**
   ```bash
   git checkout -b feature/nueva-funcionalidad
   ```

2. **Desarrollo**
   ```bash
   composer run dev
   ```

3. **Testing**
   ```bash
   composer run test
   ```

4. **Build y deploy**
   ```bash
   npm run build
   php artisan config:cache
   php artisan route:cache
   ```

---

## Arquitectura del Proyecto

- **Laravel 12.x** - Framework principal
- **Vue 3.x** - Frontend con Inertia.js
- **TailwindCSS** - Estilos
- **Vite** - Build tool
- **Laravel Fortify** - Autenticación
- **Spatie Permissions** - Gestión de permisos
- **Stancl Tenancy** - Multi-tenancy

---

## Contacto y Soporte

Para problemas o preguntas:
- Revisar los logs en `storage/logs/laravel.log`
- Ejecutar `php artisan tinker` para debugging
- Consultar la documentación oficial de Laravel

---

**Nota**: Esta guía asume que tienes instalados PHP, Node.js, Composer y NPM en tu sistema. Para Docker, asegúrate de tener Docker y Docker Compose instalados.
