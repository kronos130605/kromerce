---
description: Backend Repository Standards (Fase 2)
---

# Backend Repository Standards

Estándares específicos para implementación de repositorios post-Fase 2.

## Anti-patrones prohibidos

### ❌ No usar `$this->model::where()` (llamada estática)

```php
// MAL - Llama al método estáticamente
return $this->model::where('store_id', $storeId)->first();
```

### ❌ No usar `$this->model->where()` directo (sin newQuery)

```php
// MAL - Usa el modelo directamente sin newQuery()
return $this->model->where('store_id', $storeId)->first();
```

### ✅ Usar `$this->model->newQuery()->where()`

```php
// BIEN - Crea query builder fresco
return $this->model->newQuery()
    ->where('store_id', $storeId)
    ->first();
```

## Métodos BaseRepository a usar

- `$this->getById($id)` — Obtener por ID con null check implícito
- `$this->getFirstBy(['field' => 'value'])` — Obtener primer registro por criterios
- `$this->getBy(['field' => 'value'])` — Obtener colección por criterios
- `$this->firstOrCreate($criteria, $defaults)` — Obtener o crear

## Null Safety

### ✅ Siempre verificar existencia antes de acceder a relaciones

```php
public function getProductsCount(int $storeId): int
{
    $store = $this->getById($storeId);
    return $store ? $store->products()->count() : 0;
}
```

### ❌ No asumir que el modelo existe

```php
// MAL - Puede lanzar NullPointerException
return $this->model::find($storeId)->products()->count();
```

## Creación de métodos específicos

### ✅ Crear método cuando:
- Requiere lógica compleja o múltiples consultas
- Necesita joins o relaciones específicas
- Implementa validaciones de negocio
- Realiza operaciones de cálculo o agregación

### ❌ No crear método wrapper simple:

```php
// INNECESARIO - Solo llama a BaseRepository
public function getByStoreId(int $storeId): Collection
{
    return $this->getBy(['store_id' => $storeId]);
}
```

## Estructura de repositorios por dominio

```
app/Repositories/
  BaseRepository.php
  Product/
    ProductRepository.php
    ProductCategoryRepository.php
  Store/
    StoreRepository.php
    StoreStatisticsRepository.php
    StoreCurrencyConfigRepository.php
  User/
    UserStoreRepository.php
```

## Registro en BusinessServiceProvider

```php
$this->app->singleton(ProductRepository::class, function ($app) {
    return new ProductRepository($app->make(Product::class));
});
```

## Checklist de implementación

- [ ] No hay `$this->model::` estático en ningún método.
- [ ] Todos los queries usan `$this->model->newQuery()`.
- [ ] Métodos con relaciones verifican null antes de acceder.
- [ ] No hay métodos wrapper que solo llamen a BaseRepository.
- [ ] Repositorio registrado como singleton en BusinessServiceProvider.
