---
description: Backend-Frontend ID Architecture (UUID System)
---

# ID Architecture Standards

## Convención de Identificadores

Este proyecto usa **UUID como Primary Key** en todas las entidades principales:

| Capa | Tipo de ID | Uso |
|------|-----------|-----|
| Base de Datos | `uuid` (string 36 chars) | Primary Key en todas las tablas |
| Backend (PHP) | `string` | Parámetros de métodos, búsquedas |
| Frontend (Vue) | `string` | Props, v-model, rutas |
| API/JSON | `string` | Todos los IDs en responses/requests |

## Tablas con UUID como Primary Key

- `products` - `$table->uuid('id')->primary()`
- `product_categories` - `$table->uuid('id')->primary()`
- `product_tags` - `$table->uuid('id')->primary()`
- `product_attributes` - `$table->uuid('id')->primary()`
- `stores` - `$table->uuid('id')->primary()`
- Todas las demás entidades del sistema

## Modelos Eloquent - Configuración UUID

```php
class Product extends Model
{
    use HasFactory, SoftDeletes;

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

## Repositorios - Métodos con UUID

### ✅ Firmas correctas

```php
// Buscar por UUID (string)
public function getById(string $id): ?Model

// Actualizar por UUID
public function updateBy(array $conditions, array $data): int

// Usar en condiciones
$this->productRepository->getFirstBy(['id' => $productId, 'store_id' => $storeId]);
```

### ❌ Firmas incorrectas

```php
// NO usar int para UUIDs
public function getById(int $id): ?Model  // ❌ ERROR

// NO intentar castear
public function updateProduct(string $id)  // ✅ Correcto
public function updateProduct(int $id)     // ❌ Incorrecto
```

## Controladores - Manejo de UUIDs

```php
// El route model binding funciona automáticamente con UUID
public function update(Request $request, Product $product)
{
    // $product->id es string UUID
    $this->service->update($product->id, $data);
}

// O recibir como parámetro string
public function update(string $productId)
{
    $this->service->update($productId, $data);
}
```

## Servicios - Parámetros UUID

```php
class ProductService
{
    // ✅ Siempre usar string para IDs
    public function getProductForStore(Store $store, string $productId): ?Model
    
    public function updateProductForStore(Store $store, string $productId, array $data): bool
    
    public function deleteProductForStore(Store $store, string $productId): bool
}
```

## Resources - Serialización de IDs

```php
class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->id,  // Asegurar string
            'category_ids' => $this->categories?->pluck('id')->map(fn($id) => (string) $id)->toArray() ?? [],
        ];
    }
}
```

## Frontend - Manejo de UUIDs (Vue)

```javascript
// Props - siempre recibir como string
const props = defineProps({
    productId: {
        type: String,  // No Number
        required: true
    }
});

// Comparación de IDs - convertir a string
const isSelected = (id) => {
    return selectedIds.value.includes(String(id));
};

// Emits
const emit = defineEmits(['update:modelValue']);
emit('update:modelValue', selectedIds.value.map(id => String(id)));
```

## Validación de Requests

```php
// Validar UUIDs como string
$rules = [
    'product_id' => 'required|string|uuid',
    'category_ids' => 'array',
    'category_ids.*' => 'string|uuid',
];
```

## Relaciones y Foreign Keys

Todas las foreign keys en la BD son UUID:

```php
// Migraciones - FK siempre como uuid
Schema::create('product_images', function (Blueprint $table) {
    $table->id();  // Auto-increment (solo para este tabla pivot)
    $table->uuid('product_id');  // FK a products
    $table->foreign('product_id')->references('id')->on('products');
});
```

## Checklist de Implementación

- [ ] Modelo tiene `$keyType = 'string'` y `$incrementing = false`
- [ ] Modelo genera UUID en `boot()` si está vacío
- [ ] Repositorios usan `string` en parámetros de ID
- [ ] Servicios usan `string` en parámetros de ID
- [ ] Resources castean IDs a `(string)`
- [ ] Frontend usa `String` en props de IDs
- [ ] Comparaciones de IDs usan `String(id)`

## Migraciones - No usar id() autoincremental

```php
// ❌ No usar esto para entidades principales
$table->id();  // BigInt autoincrement

// ✅ Usar esto
$table->uuid('id')->primary();
```

## Excepciones

Algunas tablas pivot o de relación pueden usar `$table->id()` autoincremental:
- `product_images` (usa id autoincrement + uuid FK)
- `product_price_history` (usa id autoincrement + uuid FK)

Pero las FK siempre apuntan a UUIDs.
