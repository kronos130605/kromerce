---
description: Authorization & Roles (Spatie)
---

# Authorization & Roles (Spatie)

Este workflow define el estándar de autorización del sistema.

## Fuente de verdad

- Roles globales del sistema con **Spatie**.
- Pertenecer a un store NO cambia el rol.

## Cómo autorizar

- **Por rol**:
  - middleware: `role:admin`
  - code: `$user->hasRole('admin')`
- **Por permiso**:
  - middleware: `permission:products.view`
  - code: `$user->can('products.view')`

## Reglas

- Prohibido autorizar con campos pivot tipo `store_users.role`.
- Store context se valida separado (membresía), no como rol.

## Checklist

- [ ] Controllers usan middleware `role:`/`permission:`
- [ ] Policies/Gates (si existen) usan Spatie
- [ ] Frontend recibe `auth.user.roles` desde Inertia, no roles por store
