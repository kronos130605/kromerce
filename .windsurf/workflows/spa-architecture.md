---
description: Business SPA Architecture (Inertia + Vue)
---

# Business SPA Architecture

Este workflow define el patrón único del **Business SPA**.

## Fuente de verdad

- Entrada única de UI business: `Inertia::render('Business/Index', ...)`
- Frontend resuelve `Business/Index` en:
  - `resources/js/modules/business/pages/Index.vue` (ver `resources/js/app.js`)

## Estructura recomendada

```
resources/js/modules/business/
  pages/
    Index.vue
  content/
    DashboardContent.vue
    ProductsContent.vue
    ...
  components/
    ...
```

## Contrato Controller -> SPA

Todo controller business debe:

- Renderizar `Business/Index`
- Definir `activeTab`
- Enviar solo props necesarias para ese tab

Ejemplo:

```php
return Inertia::render('Business/Index', [
    'activeTab' => 'products',
    'products' => $products,
]);
```

## Reglas

- Prohibido crear nuevas páginas “sueltas” para business fuera de `Business/Index`, salvo casos excepcionales.
- Si se requiere una vista nueva, primero intentar modelarla como:
  - `activeTab` nuevo
  - `*Content.vue` nuevo

## Checklist

- [ ] Ruta renderiza `Business/Index`
- [ ] `activeTab` es estable (string)
- [ ] Props mínimas (sin payload gigante)
- [ ] i18n sin strings hardcode
