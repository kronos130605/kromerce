---
description: Component Workflow (Vue + Inertia + i18n modular)
---

# Component Workflow

Workflow estándar para crear/editar componentes Vue en Kromerce.

## Pre-requisitos

- Leer `development-standards.md` (dark mode, accesibilidad, i18n).
- Verificar si ya existe un componente similar para reutilizar.

## Dónde vive el componente

- **Business SPA**:
  - UI principal en `resources/js/modules/business/pages/Index.vue`
  - Contenido en `resources/js/modules/business/content/*Content.vue`
- **Componentes compartidos**:
  - `resources/js/components/`
- **Componentes por módulo**:
  - `resources/js/modules/[module]/components/`

## i18n (modular)

Las traducciones viven en:

```
resources/js/i18n/locales/
  es/*.json
  en/*.json
```

Reglas:

- Agrega keys en **ambos idiomas**.
- Usa llaves estables tipo: `products.list.empty_state.title`.

## Checklist de implementación

- [ ] Dark mode completo (clases `dark:`)
- [ ] Accesibilidad mínima (foco visible, `aria-*` si aplica)
- [ ] i18n: sin strings hardcode
- [ ] Props/emits con nombres claros

## Checklist de integración en Business SPA

- [ ] El tab se resuelve por `activeTab`.
- [ ] El controller renderiza `Business/Index` con props mínimas.
