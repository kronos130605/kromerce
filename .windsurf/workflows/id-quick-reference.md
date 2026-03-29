# Quick Reference: ID Patterns by Table

## Stores & Users → Dual ID (id + uuid)
| Campo | Tipo | Uso |
|-------|------|-----|
| `id` | bigint | Backend queries, FKs |
| `uuid` | string(36) | APIs, Frontend |

**FKs a estas tablas:** `unsignedBigInteger` apunta a `id`

---

## Products, Categories, Tags, Orders → UUID PK
| Campo | Tipo | Uso |
|-------|------|-----|
| `id` | uuid (string) | Todo (PK) |

**FKs a estas tablas:** `uuid()` apunta a `id`

---

## Tablas Child/Relación → ID Autoincrement
| Campo | Tipo | Uso |
|-------|------|-----|
| `id` | bigint | PK simple |
| `*_id` | int/uuid | FK a tabla padre |

---

## Tipos de Parámetros en PHP

```php
// Para Stores/Users (Dual ID)
public function getByUuid(string $uuid): ?Store
public function getById(int $id): ?Store
public function update(int $storeId, array $data): bool  // usa id interno

// Para Products/Categories (UUID)
public function getById(string $id): ?Product  // siempre string
public function update(string $productId, array $data): bool
```

## Serialización en Resources

```php
// Store (Dual ID) - enviar uuid al frontend
return [
    'id' => $this->uuid,      // Frontend usa UUID
    // 'internal_id' => $this->id,  // No enviar
];

// Product (UUID) - enviar id directamente
return [
    'id' => (string) $this->id,  // Ya es UUID
];
```

## Tipos de Columnas en Migraciones

```php
// FK a stores/users (Dual ID)
$table->unsignedBigInteger('store_id');
$table->foreign('store_id')->references('id')->on('stores');

// FK a products/categories (UUID)
$table->uuid('product_id');
$table->foreign('product_id')->references('id')->on('products');
```
