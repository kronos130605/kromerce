---
description: Config Standards (centralización de configuraciones)
---

# Config Standards

Estándar para centralización de configuraciones en Kromerce.

## Principio fundamental

> **Todo array o configuración reutilizable debe ir en `config/` correspondiente.**

No duplicar arrays de configuración en múltiples archivos (modelos, services, controllers).

## Ejemplos de configuraciones centralizadas

### Currencies (`config/currencies.php`)

```php
return [
    'supported' => [
        'USD' => [
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'flag' => '🇺🇸',
        ],
        // ... más monedas
    ],
    'default' => 'USD',
    'default_display' => ['USD', 'EUR', 'COP'],
];
```

**Uso en el código:**

```php
// Obtener todas las monedas soportadas
$currencies = config('currencies.supported');

// Obtener solo los códigos
$codes = array_keys(config('currencies.supported'));

// Obtener símbolo específico
$symbol = config("currencies.supported.USD.symbol", '$');
```

## Cuándo crear un config nuevo

### ✅ Crear config cuando:
- El array se usa en 2+ archivos diferentes
- Los valores pueden cambiar por entorno (dev/prod)
- Son listas de opciones válidas (currencies, languages, etc.)
- Son configuraciones de comportamiento del sistema

### ❌ No crear config cuando:
- Es un array usado solo en un archivo y no cambia
- Son valores efímeros o temporales
- Puede resolverse con una constante de clase

## Estructura recomendada

```php
return [
    // Datos principales (arrays asociativos)
    'supported' => [...],
    'available' => [...],
    
    // Valores por defecto
    'default' => 'value',
    'default_list' => ['value1', 'value2'],
    
    // Configuraciones de comportamiento
    'enabled' => true,
    'max_items' => 10,
];
```

## Archivos que deben usar configs (no arrays hardcode)

| Config | Archivos que la usan |
|--------|---------------------|
| `currencies.supported` | BusinessCurrencyConfig, CurrencyController, CurrencyRateService |
| `currencies.default` | BusinessCurrencyConfigRepository, StoreService |
| `currencies.default_display` | BusinessCurrencyConfigRepository |

## Checklist

- [ ] No hay arrays de currencies duplicados en el código.
- [ ] Todos los valores de monedas usan `config('currencies')`.
- [ ] Nuevos configs siguen la estructura `supported/default`.
- [ ] Documentado en este workflow si es un config nuevo.
