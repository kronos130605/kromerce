#!/bin/bash

# Script específico para configurar Nginx en WSL2 para Kromerce
# Uso: bash scripts/setup-nginx-wsl.sh

set -e

echo "🚀 Configurando Nginx para Kromerce en WSL2..."

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_message() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_wsl() {
    echo -e "${BLUE}[WSL2]${NC} $1"
}

# Verificar si estamos en WSL2
if ! grep -q Microsoft /proc/version; then
    print_error "Este script está diseñado para WSL2"
    exit 1
fi

# Obtener directorio del proyecto
PROJECT_DIR=$(pwd)
NGINX_CONF="$PROJECT_DIR/nginx.conf"

if [ ! -f "$NGINX_CONF" ]; then
    print_error "No se encuentra nginx.conf en el directorio del proyecto"
    exit 1
fi

print_wsl "Detectado entorno WSL2"

# 1. Actualizar e instalar Nginx
print_message "Actualizando paquetes e instalando Nginx..."
sudo apt update
sudo apt install -y nginx

# 2. Instalar PHP-FPM
print_message "Instalando PHP-FPM y extensiones..."
sudo apt install -y php8.3-fpm php8.3-mysql php8.3-pgsql php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-zip

# 3. Configurar Nginx para WSL2
print_message "Configurando Nginx para Kromerce..."
sudo cp "$NGINX_CONF" /etc/nginx/sites-available/kromerce

# Habilitar el sitio
sudo ln -sf /etc/nginx/sites-available/kromerce /etc/nginx/sites-enabled/kromerce

# Deshabilitar sitio default
sudo rm -f /etc/nginx/sites-enabled/default

# 4. Configurar permisos
print_message "Configurando permisos para el proyecto..."
if [ -d "$PROJECT_DIR/storage" ]; then
    sudo chown -R www-data:www-data "$PROJECT_DIR/storage"
    sudo chown -R www-data:www-data "$PROJECT_DIR/bootstrap/cache"
    sudo chmod -R 775 "$PROJECT_DIR/storage"
    sudo chmod -R 775 "$PROJECT_DIR/bootstrap/cache"
else
    print_warning "No se encuentra el directorio storage"
fi

# 5. Configurar para acceso desde Windows
print_wsl "Configurando acceso desde Windows host..."
WSL_IP=$(hostname -I | awk '{print $1}')
WINDOWS_IP=$(ip route show | grep default | awk '{print $3}')

# Modificar nginx.conf para permitir conexiones desde Windows
sudo sed -i "s/listen 80;/listen 80;\n    listen $WSL_IP:80;/" /etc/nginx/sites-available/kromerce

# 6. Iniciar servicios
print_message "Iniciando servicios..."
sudo systemctl start nginx
sudo systemctl enable nginx
sudo systemctl start php8.3-fpm
sudo systemctl enable php8.3-fpm

# 7. Verificar configuración
print_message "Verificando configuración..."
if sudo nginx -t; then
    print_message "✅ Configuración de Nginx válida"
    sudo systemctl reload nginx
else
    print_error "❌ Error en la configuración de Nginx"
    exit 1
fi

# 8. Configurar Windows Host (instrucciones)
print_wsl "Configuración para acceso desde Windows:"
echo ""
echo "📋 Para acceder desde Windows:"
echo "  1. Desde PowerShell (como Administrador):"
echo "     netsh interface portproxy add v4tov4 listenport=80 listenaddress=0.0.0.0 connectport=80 connectaddress=$WSL_IP"
echo ""
echo "  2. O acceder directamente usando la IP de WSL:"
echo "     http://$WSL_IP"
echo ""
echo "  3. Para eliminar el proxy después:"
echo "     netsh interface portproxy delete v4tov4 listenport=80 listenaddress=0.0.0.0"

# 9. Configurar firewall de Windows (instrucciones)
echo ""
print_warning "Firewall de Windows:"
echo "  Si tienes problemas, permite el puerto 80 en el firewall de Windows"
echo "  o ejecuta en PowerShell (Admin):"
echo "  New-NetFirewallRule -DisplayName 'WSL2 Kromerce' -Direction Inbound -LocalPort 80 -Protocol TCP -Action Allow"

# 10. Información de acceso
echo ""
print_message "🎉 Configuración completada!"
echo ""
print_wsl "Información de acceso:"
echo "  - IP de WSL2: $WSL_IP"
echo "  - Desde WSL: http://localhost"
echo "  - Desde Windows: http://$WSL_IP"
echo "  - Dominio local: http://kromerce.local (si configuras hosts)"

# 11. Configurar hosts locales (opcional)
echo ""
print_warning "Configuración de hosts locales (opcional):"
echo "  Agrega a C:\\Windows\\System32\\drivers\\etc\\hosts (como Administrador):"
echo "  $WSL_IP kromerce.local"

# 12. Comandos útiles
echo ""
print_message "📋 Comandos útiles:"
echo "  - Ver estado: sudo systemctl status nginx"
echo "  - Ver logs: sudo journalctl -u nginx -f"
echo "  - Recargar: sudo systemctl reload nginx"
echo "  - Test config: sudo nginx -t"
echo "  - Ver IP WSL: hostname -I"

echo ""
print_message "✨ ¡Kromerce está listo en WSL2 con Nginx!"
