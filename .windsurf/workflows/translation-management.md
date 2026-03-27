---
description: i18n Modular (estructura + reglas + performance)
---

# i18n Modular

Este workflow define el estándar de traducciones (Vue i18n) en Kromerce.

## Estructura real del repo

```
resources/js/i18n/
  index.js
  locales/
    es/
    en/
```

## Reglas

- Prohibido hardcode de strings visibles al usuario.
- Toda key debe existir en `es` y `en`.
- Nomenclatura:
  - `module.section.key`
  - ejemplo: `products.table.empty.title`

## Performance (solo enviar lo necesario)

- No enviar “diccionario completo” al frontend si no se necesita.
- Preferir enviar módulos/segmentos requeridos por la vista actual.

## Checklist al agregar traducciones

- [ ] Key agregada en `resources/js/i18n/locales/es/...`
- [ ] Key agregada en `resources/js/i18n/locales/en/...`
- [ ] Componente usa `t('...')`
- [ ] Se verificó en ambos idiomas
