# Configuración de Internacionalización (i18n) para Kromerce

## 📋 Pasos para Configurar i18n

### 1. Instalar dependencias de Vue i18n
```bash
npm install vue-i18n@9
```

### 2. Ejecutar comandos de Laravel
```bash
# Limpiar caché y autoloader
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerar autoloader
composer dump-autoload
```

### 3. Configurar variables de entorno
Asegúrate de tener estas variables en tu archivo `.env`:
```env
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
```

### 4. Verificar configuración
Los siguientes archivos han sido creados/actualizados:

#### Backend:
- `config/i18n.php` - Configuración de i18n
- `app/Providers/I18nServiceProvider.php` - Provider para compartir traducciones
- `app/Http/Middleware/SetLocale.php` - Middleware para detectar idioma
- `bootstrap/providers.php` - Registro del nuevo provider

#### Frontend:
- `resources/js/i18n.js` - Configuración de Vue i18n
- `resources/js/locales/en/dashboard.json` - Traducciones en inglés
- `resources/js/locales/es/dashboard.json` - Traducciones en español
- `resources/js/app.js` - Configuración actualizada para usar i18n dinámico

### 5. Probar la configuración
1. Reinicia el servidor de desarrollo:
```bash
npm run dev
```

2. Verifica que no aparezcan más warnings de `[intlify]`

3. Cambia el idioma en el navegador o agrega `?locale=es` a la URL

### 6. Uso en Componentes
Los componentes ya están configurados para usar traducciones:

```vue
<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <h1>{{ t('dashboard.welcome') }}</h1>
    <p>{{ t('dashboard.discover_products') }}</p>
</template>
```

### 7. Modo Oscuro
Todos los componentes incluyen clases `dark:` para soporte de modo oscuro:
- `text-gray-900 dark:text-white`
- `bg-white dark:bg-gray-800`
- `border-gray-200 dark:border-gray-700`

## 🎯 Resultado Esperado
- ✅ Sin warnings de `[intlify]`
- ✅ Traducciones funcionando en inglés y español
- ✅ Cambio de idioma dinámico
- ✅ Modo oscuro completo
- ✅ Componentes modulares y mantenibles

## 🔧 Solución de Problemas Comunes

### Si aparecen warnings de `[intlify]`:
1. Ejecuta `php artisan cache:clear`
2. Ejecuta `composer dump-autoload`
3. Reinicia el servidor de desarrollo

### Si las traducciones no se muestran:
1. Verifica que el middleware `SetLocale` esté registrado en `bootstrap/app.php`
2. Verifica que las rutas tengan el middleware web
3. Revisa la consola del navegador para errores de JavaScript

### Si el modo oscuro no funciona:
1. Asegúrate de tener Tailwind CSS configurado para modo oscuro
2. Verifica que las clases `dark:` estén aplicadas correctamente
3. Revisa el archivo `tailwind.config.js` para la configuración del modo oscuro
