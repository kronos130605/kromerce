---
description: Dev Environment (WSL-only)
---

# Dev Environment (WSL-only)

Este workflow es el estándar de comandos para desarrollo en Kromerce usando **WSL**.

## Reglas

- Los comandos los ejecutas **manualmente** en tu consola WSL.
- Backend corre con Nginx/PHP-FPM (no usar `php artisan serve`).

## Node (NVM)

Siempre ejecutar Node/NPM con NVM cargado:

```bash
wsl bash -c "source ~/.nvm/nvm.sh && node --version"
wsl bash -c "source ~/.nvm/nvm.sh && npm --version"
```

## Comandos comunes

```bash
# Artisan
wsl php artisan migrate
wsl php artisan cache:clear
wsl php artisan config:clear

# Logs
wsl tail -50 storage/logs/laravel.log

# NPM (con NVM)
wsl bash -c "source ~/.nvm/nvm.sh && npm install"
wsl bash -c "source ~/.nvm/nvm.sh && npm run build"

# Git
wsl git status
wsl git add .
wsl git commit -m "message"
```

## Nunca ejecutar

```bash
# No levantar servidor Laravel
wsl php artisan serve
```