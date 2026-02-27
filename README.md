# Kromerce

Aplicación web Laravel con Vue.js para gestión de e-commerce multi-tenant.

## 🚀 Configuración Rápida

Para una guía completa de instalación y configuración, consulta [SETUP.md](./SETUP.md).

### Requisitos Mínimos
- PHP 8.2+
- Node.js 20.x
- MySQL 8.0+ o PostgreSQL 13+
- Composer 2.x

### Instalación Rápida
```bash
git clone <repository-url>
cd kromerce
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

## 📦 Tecnologías

- **Backend**: Laravel 12.x, PHP 8.3+
- **Frontend**: Vue 3.x, Inertia.js, TailwindCSS
- **Build Tool**: Vite 7.x
- **Base de Datos**: MySQL/PostgreSQL
- **Autenticación**: Laravel Fortify
- **Permisos**: Spatie Laravel Permission
- **Multi-tenancy**: Stancl Tenancy

## 🐳 Docker

Para desarrollo local con Docker:
```bash
# Construir imagen
docker build -t kromerce .

# Ejecutar con Docker Compose (desarrollo local)
docker-compose -f docker-compose.dev.yml up -d

# Acceder a la aplicación
# http://localhost:8000
```

**Nota**: Usa `docker-compose.dev.yml` para desarrollo local. No usar `docker-compose.yml` en producción existente.

## 📚 Documentación

- [Guía de Configuración](./SETUP.md) - Instalación completa
- [Configuración de Base de Datos](./SETUP.md#configuración-de-base-de-datos-detallada)
- [Configuración de Nginx](./SETUP.md#configuración-de-nginx) - WSL2/Linux/Docker
- [Scripts Útiles](./SETUP.md#scripts-útiles)
- [Scripts Automatizados](./SETUP.md#scripts-automatizados) - Configuración automática

## 🛠️ Scripts Disponibles

```bash
# Desarrollo completo
composer run dev

# Instalación automatizada
composer run setup

# Testing
composer run test

# Build de assets
npm run build

# Configuración de Nginx (Linux general)
sudo bash scripts/setup-nginx.sh

# Configuración de Nginx (WSL2 específico)
bash scripts/setup-nginx-wsl.sh
```

## 📄 Licencia

MIT License - ver [LICENSE](LICENSE) para más detalles.
