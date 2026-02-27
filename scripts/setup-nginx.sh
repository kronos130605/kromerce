#!/bin/bash

# Script de configuración de Nginx para Kromerce en WSL2/Linux
# Uso: sudo bash scripts/setup-nginx.sh

set -e

echo "🚀 Configurando Nginx para Kromerce..."

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para imprimir mensajes
print_message() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Verificar si se ejecuta como root
if [[ $EUID -ne 0 ]]; then
   print_error "Este script debe ejecutarse como root (sudo)"
   exit 1
fi

# Obtener directorio del proyecto
PROJECT_DIR=$(pwd)
NGINX_CONF="$PROJECT_DIR/nginx.conf"

if [ ! -f "$NGINX_CONF" ]; then
    print_error "No se encuentra nginx.conf en el directorio del proyecto"
    exit 1
fi

# 1. Instalar Nginx
print_message "Instalando Nginx..."
apt update
apt install -y nginx

# 2. Instalar PHP-FPM si no está presente
print_message "Verificando PHP-FPM..."
if ! command -v php-fpm &> /dev/null; then
    print_message "Instalando PHP-FPM..."
    apt install -y php8.3-fpm php8.3-mysql php8.3-pgsql php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-zip
fi

# 3. Configurar Nginx
print_message "Configurando Nginx para Kromerce..."
cp "$NGINX_CONF" /etc/nginx/sites-available/kromerce

# Habilitar el sitio
ln -sf /etc/nginx/sites-available/kromerce /etc/nginx/sites-enabled/kromerce

# Deshabilitar sitio default
rm -f /etc/nginx/sites-enabled/default

# 4. Configurar permisos
print_message "Configurando permisos..."
if [ -d "$PROJECT_DIR/storage" ]; then
    chown -R www-data:www-data "$PROJECT_DIR/storage"
    chown -R www-data:www-data "$PROJECT_DIR/bootstrap/cache"
    chmod -R 775 "$PROJECT_DIR/storage"
    chmod -R 775 "$PROJECT_DIR/bootstrap/cache"
else
    print_warning "No se encuentra el directorio storage. Asegúrate de estar en el directorio del proyecto."
fi

# 5. Crear directorios necesarios
print_message "Creando directorios necesarios..."
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views

# 6. Iniciar servicios
print_message "Iniciando servicios..."
systemctl start nginx
systemctl enable nginx
systemctl start php8.3-fpm
systemctl enable php8.3-fpm

# 7. Verificar configuración
print_message "Verificando configuración de Nginx..."
if nginx -t; then
    print_message "✅ Configuración de Nginx válida"
    systemctl reload nginx
else
    print_error "❌ Error en la configuración de Nginx"
    exit 1
fi

# 8. Mostrar información de acceso
print_message "🎉 Configuración completada!"

# Obtener IP para WSL2
if grep -q Microsoft /proc/version; then
    WSL_IP=$(hostname -I | awk '{print $1}')
    print_message "Estás en WSL2. Puedes acceder a Kromerce desde:"
    print_message "  - Desde WSL: http://localhost"
    print_message "  - Desde Windows: http://$WSL_IP"
else
    print_message "Puedes acceder a Kromerce en: http://localhost"
fi

# 9. Opcional: Configurar hosts locales
echo ""
print_warning "Opcional: Para configurar un dominio local, agrega a /etc/hosts:"
echo "127.0.0.1 kromerce.local"

# 10. Opcional: Configurar SSL
echo ""
print_warning "Opcional: Para configurar SSL con Let's Encrypt:"
echo "sudo apt install certbot python3-certbot-nginx"
echo "sudo certbot --nginx -d kromerce.local"

echo ""
print_message "📋 Comandos útiles:"
echo "  - Ver estado: sudo systemctl status nginx"
echo "  - Ver logs: sudo journalctl -u nginx -f"
echo "  - Recargar: sudo systemctl reload nginx"
echo "  - Test config: sudo nginx -t"

echo ""
print_message "✨ ¡Listo! Kromerce está configurado con Nginx."
