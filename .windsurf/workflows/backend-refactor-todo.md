---
description: Backend Architecture (Controllers, Services, Repositories, Middleware)
---

# Backend Architecture

Este workflow define **la arquitectura estándar** del backend. Es la referencia para decisiones de diseño y refactor.

## Decisiones acordadas (fuente de verdad)

- **Roles**: son globales del sistema con **Spatie**.
  - Un usuario puede pertenecer a un store, pero el **rol no depende** del store.
- **Business UI**: toda funcionalidad business entra por el **SPA `Business/Index`** (Inertia).
- **Store context**: existe un “store actual” para usuarios business, pero es **contexto**, no autorización.

## Capas y responsabilidades

### Controllers (`app/Http/Controllers`)

- Responsables de:
  - Validar request (FormRequest)
  - Llamar a Services
  - Responder Inertia/JSON
- Reglas:
  - No queries complejas aquí.
  - No reglas de negocio aquí.

### Services (`app/Services`)

- Responsables de:
  - Reglas de negocio
  - Orquestar repositorios
  - Manejar casos de uso
- Reglas:
  - No renderizar vistas.
  - No depender de `Request` salvo que sea imprescindible.
  - **No hacer consultas directas a modelos** — usar siempre Repositories.

### Repositories (`app/Repositories`)

- Responsables de:
  - Acceso a datos (Eloquent/Query Builder)
  - Queries reutilizables
- Reglas:
  - Evitar métodos wrapper que solo llamen a `BaseRepository`.
  - Crear métodos específicos solo si agregan lógica real (joins, scopes, agregaciones, etc.).
  - **Siempre usar `$this->model->newQuery()`** — nunca `$this->model::where()` o `$this->model->where()` directo.
  - Usar `$this->getById()` para búsquedas por ID con null checks.

### Middleware (`app/Http/Middleware`)

- Responsables de:
  - Autenticación básica / gating
  - Resolver **store context** (si aplica)
- Reglas:
  - Autorización por rol/permisos debe usar Spatie (`role:*`, `permission:*`).
  - No mezclar “store context” con “role checks”.

## Contratos mínimos

### Autorización

- Usar Spatie (`$user->hasRole()`, `$user->can()`) y middlewares `role:` / `permission:`.
- “Pertenencia a store” se valida como una **política separada**.

### Inertia

- El backend debe enviar **solo props necesarias**.
- El SPA Business se renderiza con:
  - `Inertia::render('Business/Index', ['activeTab' => '...'])`

### Config centralizado

- **Currencies**: usar `config('currencies.supported')`, `config('currencies.default')`
- No duplicar arrays de monedas en modelos/services/controllers
- Todo array reutilizable debe ir en `config/` correspondiente

## Checklist de refactor

- [ ] No hay checks de rol por store (pivot) usados para autorizar.
- [ ] Store context se resuelve de forma única y consistente.
- [ ] Controllers delgados, Services con reglas, Repos con datos.
- [ ] No hay consultas directas a modelos en servicios.
- [ ] No hay métodos inexistentes llamados en repositorios.
- [ ] Config de currencies centralizado en `config/currencies.php`.
