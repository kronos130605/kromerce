---
description: Multi-tenancy Schema (Store-based architecture)
---

# Multi-tenancy Schema

Documentación del esquema de multi-tenancy post-Fase 2.

## Arquitectura

**Modelo**: Single-database multi-tenancy con domain resolution
**Tenant**: `App\Models\Store` (reemplaza tabla `tenants` legacy)
**Resolución**: Por dominio (hostname) + sesión de usuario

## Tablas principales

### stores (tenant principal)
```php
- id, uuid, name, slug
- owner_id → users.id
- business_type, status, verified_business
- data (json para configuraciones adicionales)
- timestamps, soft_deletes
```

### domains
```php
- id, domain (hostname único)
- store_id → stores.id
- timestamps
```

### store_users (miembros)
```php
- id, store_id → stores.id, user_id → users.id
- is_active, joined_at
- timestamps
```

## Resolución de store actual

El store se resuelve en este orden:

1. **Dominio**: Si el hostname no está en `config('tenancy.central_domains')`, buscar en `domains`
2. **Sesión**: Si hay `session('current_store_id')` y el usuario tiene acceso
3. **Default**: Primer store del usuario (`getUserFirstStore`)

## Configuración

```php
// config/tenancy.php
'tenant_model' => \App\Models\Store::class,
'central_domains' => ['kromerce.test', 'localhost', '127.0.0.1'],
```

## Legacy (eliminado)

| Componente | Estado |
|------------|--------|
| Tabla `tenants` | Eliminada en migración cleanup |
| Tabla `tenant_users` | Eliminada en migración cleanup |
| Modelo `Tenant` | No existe, usa `Store` |
| Helper `tenant()` | Deprecado, usa `Store` directamente |
| `Domain::tenant()` | Alias deprecado, usa `Domain::store()` |

## Servicios involucrados

- `StoreService::resolveCurrentStoreForRequest()` - Resolución principal
- `StoreService::getByDomain()` - Lookup por hostname
- `UserStoreRepository::userHasAccessToStore()` - Validar acceso

## Checklist de verificación

- [ ] `config/tenancy.php` apunta a `Store::class`.
- [ ] Tablas `tenants` y `tenant_users` no existen en DB.
- [ ] No hay llamadas a `tenant()` en código (excepto alias deprecado).
- [ ] Resolución de store funciona por dominio y sesión.
