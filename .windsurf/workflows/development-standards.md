---
description: Frontend Standards (Dark Mode, Accessibility, i18n)
---

# Frontend Standards

Este workflow es la **fuente única de verdad** para estándares de Frontend en Kromerce:

- Dark mode obligatorio
- Accesibilidad mínima
- i18n modular (no strings hardcode)
- Componentes consistentes para el **Business SPA**

## Reglas obligatorias

### Dark Mode (Tailwind)

Usa siempre pares light/dark. Referencia base:

- **Background primario**: `bg-white dark:bg-gray-800`
- **Background secundario**: `bg-gray-50 dark:bg-gray-900`
- **Texto primario**: `text-gray-900 dark:text-white`
- **Texto secundario**: `text-gray-600 dark:text-gray-300`
- **Bordes**: `border-gray-200 dark:border-gray-700`
- **Inputs**: `bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600`
- **Focus**: `focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400`

Checklist:

- [ ] Ningún color sin variante `dark:`
- [ ] Hover/focus/disabled con variante `dark:`
- [ ] Se probó visualmente en light/dark

### Accesibilidad (mínimo)

- Usa HTML semántico (`button`, `label`, `section`, `nav`)
- Todo control interactivo debe tener:
  - `aria-label` si el texto no es suficiente
  - foco visible (`focus:ring-*`)
- Formularios:
  - `label` asociado al input
  - errores visibles y anunciables

Checklist:

- [ ] Navegable por teclado
- [ ] Foco visible en light/dark
- [ ] `aria-*` donde aplique

### i18n (modular) obligatorio

**No hardcode strings** en templates/JS para texto visible al usuario.

Arquitectura i18n del repo:

```
resources/js/i18n/
  index.js
  locales/
    es/
    en/
```

Reglas:

- Llaves con dot-notation: `products.form.name.label`
- Usa nombres estables (no basados en copy literal).
- Toda llave debe existir en `es` y `en`.

Checklist:

- [ ] No hay strings UI hardcode
- [ ] Llaves existen en ambos idiomas

## Revisión antes de merge

- [ ] Dark mode completo
- [ ] Accesibilidad mínima cumplida
- [ ] i18n modular aplicado
- [ ] Integración consistente con el Business SPA (si aplica)
