---
description: Database ID Architecture Patterns (Dual ID vs UUID)
---

# Database ID Architecture

## Resumen de Patrones

El sistema usa **DOS patrones de IDs** según el tipo de entidad:

| Patrón | Tablas | Descripción |
|--------|--------|-------------|
| **Dual ID** (id + uuid) | stores, users | `id` bigint PK interno + `uuid` string público |
| **UUID único** | products, categories, orders | `uuid('id')->primary()` solo UUID |
| **ID autoincrement** | tablas child/pivot | `id()` PK simple con FK a padre |

---

## Patrón 1: Dual ID (id + uuid)

### Tablas con este patrón:
- `stores`
- `users`

### Estructura:
```php
Schema::create('stores', function (Blueprint $table) {
    $table->id();                    // bigint unsigned, Primary Key (interno)
    $table->uuid('uuid')->unique();  // string(36), UUID público (APIs/Frontend)
    // ... campos
});
```

### Convención de Uso:

| Capa | Campo a usar | Tipo |
|------|-------------|------|
| **Backend queries** | `id` | `int` |
| **APIs / Frontend** | `uuid` | `string` |
| **Foreign Keys** | `store_id` (apunta a `stores.id`) | `int` |

### Ejemplo de implementación:

#### Modelo:
```php
class Store extends Model
{
    // Por defecto usa 'id' (bigint)
    protected $primaryKey = 'id';
    
    // Accesor para UUID
    public function getRouteKeyName()
    {
        return 'uuid';  // Para route model binding con UUID
    }
}
```

#### Repositorio:
```php
// Buscar por UUID (para APIs)
public function getByUuid(string $uuid): ?Store
{
    return $this->model->where('uuid', $uuid)->first();
}

// Buscar por ID (para queries internas)
public function getById(int $id): ?Store
{
    return $this->model->find($id);
}

// Buscar para tienda específica (usa int id)
public function getForStore(int $storeId): ?Store
{
    return $this->model->find($storeId);
}
```

#### Controlador:
```php
// Route model binding automático con UUID
public function show(Store $store)
{
    // $store->id = int (uso interno)
    // $store->uuid = string (para APIs)
    return new StoreResource($store);
}
```

#### Resource:
```php
class StoreResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,  // Frontend recibe UUID
            'internal_id' => $this->id,  // Solo si es necesario
            'name' => $this->name,
        ];
    }
}
```

---

## Patrón 2: UUID como Primary Key

### Tablas con este patrón:
- `products`
- `product_categories`
- `product_tags`
- `product_attributes`
- `orders`
- `business_currency_configs`
- etc.

### Estructura:
```php
Schema::create('products', function (Blueprint $table) {
    $table->uuid('id')->primary();  // UUID es la Primary Key
    $table->unsignedBigInteger('store_id');  // FK a stores.id (int)
    // ... campos
});
```

### Convención de Uso:

| Capa | Campo | Tipo |
|------|-------|------|
| **Todas las capas** | `id` (UUID) | `string` |
| **Foreign Keys** | `product_id`, `category_id` | `uuid` |

### Ejemplo de implementación:

#### Modelo:
```php
class Product extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'id';

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid()->toString();
            }
        });
    }
}
```

#### Repositorio:
```php
// Todos los métodos usan string
public function getById(string $id): ?Product
{
    return $this->model->find($id);
}

public function updateById(string $id, array $data): bool
{
    return $this->model->where('id', $id)->update($data);
}
```

#### Resource:
```php
class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->id,  // Ya es UUID
            'store_id' => $this->store_id,  // Esto es int (FK a stores)
        ];
    }
}
```

---

## Patrón 3: ID Autoincrement (Tablas Child/Pivot)

### Tablas con este patrón:
- `product_images`
- `product_category_product` (usa composite PK)
- `store_contacts`
- `store_social_media`
- `store_payment_methods`
- etc.

### Estructura:
```php
Schema::create('product_images', function (Blueprint $table) {
    $table->id();  // bigint autoincrement, PK simple
    $table->uuid('product_id');  // FK a products.id (UUID)
    // ... campos
});
```

### Características:
- PK simple autoincrement para índices rápidos
- FKs apuntan a UUIDs de tablas padre

---

## Matriz de Tipos por Tabla

| Tabla | PK | Tipo PK | UUID? | FKs |
|-------|-----|---------|-------|-----|
| `users` | `id` | bigint | ✅ `uuid` | - |
| `stores` | `id` | bigint | ✅ `uuid` | `owner_id` (int) |
| `products` | `id` | uuid | - | `store_id` (int) |
| `product_categories` | `id` | uuid | - | `parent_id` (uuid) |
| `product_tags` | `id` | uuid | - | `store_id` (int) |
| `product_images` | `id` | bigint | - | `product_id` (uuid) |
| `orders` | `id` | uuid | - | `store_id` (int), `user_id` (int) |
| `store_contacts` | `id` | bigint | - | `store_id` (int) |
| `store_currency_configs` | `id` | uuid | - | `store_id` (int) |

---

## Reglas para Desarrolladores

### 1. Identificar el patrón de la tabla:

```php
// ¿Tiene campo 'uuid' separado?
if ($model->getTable() === 'stores' || $model->getTable() === 'users') {
    // Patrón Dual: usar uuid para APIs, id para queries
} else {
    // Patrón UUID: usar id directamente
}
```

### 2. En Repositorios:

```php
class ProductRepository
{
    // Product usa UUID
    public function getById(string $id): ?Product
    {
        return $this->model->find($id);
    }
}

class StoreRepository  
{
    // Store usa Dual ID
    public function getByUuid(string $uuid): ?Store
    {
        return $this->model->where('uuid', $uuid)->first();
    }
    
    public function getById(int $id): ?Store
    {
        return $this->model->find($id);
    }
}
```

### 3. En Controllers:

```php
class ProductController
{
    // Product UUID
    public function update(string $productId)  // string
    {
        $this->service->update($productId, $data);
    }
}

class StoreController
{
    // Store con UUID
    public function update(string $storeUuid)  // string UUID
    {
        $store = $this->repository->getByUuid($storeUuid);
        $this->service->update($store->id, $data);  // int para servicio
    }
}
```

### 4. En Resources:

```php
// Product (UUID único)
'id' => (string) $this->id,

// Store (Dual ID)
'id' => $this->uuid,  // Frontend recibe UUID
'store_id' => $this->id,  // Si es necesario para debug
```

---

## Checklist de Migración

Cuando crees nuevas tablas:

- [ ] ¿Es tabla maestra como users/stores? → Dual ID (`id` + `uuid`)
- [ ] ¿Es entidad global como products? → UUID PK único
- [ ] ¿Es tabla child/pivot? → ID autoincrement + FKs correctos
- [ ] Las FK a `stores` y `users` son `unsignedBigInteger`
- [ ] Las FK a entidades UUID son `uuid()`

## Ejemplos de Migraciones Correctas

### Tabla Maestra (Dual ID):
```php
Schema::create('new_entities', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();
    $table->unsignedBigInteger('store_id');  // FK a stores.id
    $table->foreign('store_id')->references('id')->on('stores');
    $table->timestamps();
});
```

### Tabla Global (UUID):
```php
Schema::create('global_items', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->unsignedBigInteger('store_id');  // FK a stores.id (int)
    $table->foreign('store_id')->references('id')->on('stores');
    $table->timestamps();
});
```

### Tabla Child (ID autoincrement):
```php
Schema::create('item_details', function (Blueprint $table) {
    $table->id();
    $table->uuid('global_item_id');  // FK a global_items.id (uuid)
    $table->foreign('global_item_id')->references('id')->on('global_items');
    $table->timestamps();
});
```
