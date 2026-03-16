---
description: Repository Factory (instanciación consistente + DI)
---

# Repository Factory

Este workflow define cómo usar `RepositoryFactory` sin inconsistencias.

## Objetivo

- Instanciación consistente de repositorios.
- Reducir dependencias en services (inyectas `RepositoryFactory`).

## Reglas

- Cada método del factory debe:
  - Instanciar el repositorio con su **Model correcto**.
  - Tener nombre consistente: `productRepository()`, `storeRepository()`, etc.
- Prohibido:
  - métodos duplicados que devuelven lo mismo
  - métodos “legacy” con naming incorrecto (ej: `userTenantRepository` si realmente es store)

## Cómo agregar un repositorio nuevo

1) Crear repositorio en su carpeta de dominio:

- `app/Repositories/Product/*`
- `app/Repositories/Store/*`
- `app/Repositories/User/*`

2) Asegurar constructor consistente:

```php
public function __construct(Model $model)
{
    parent::__construct($model);
}
```

3) Agregar método en `app/Factories/RepositoryFactory.php`:

- Debe devolver el tipo concreto.
- Debe crear el `new Model()` correspondiente.

## Testing

- En unit tests, mockea el factory y define qué repositorio devuelve.

## Checklist

- [ ] Factory sin duplicados
- [ ] Cada repo se crea con su Model correcto
- [ ] Services no instancian repos directamente
