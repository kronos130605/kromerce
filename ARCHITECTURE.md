# 🏗️ ARQUITECTURA KROMERCE

## 📐 Patrón de Capas (3-Tier Architecture)

```
Controller → Service → Repository → Model → Database
```

### **1. Controller (Capa de Presentación)**
- ✅ **Solo validación y respuestas HTTP**
- ✅ Debe estar lo más limpio posible
- ❌ NO debe tener lógica de negocio
- ❌ NO debe tener consultas a BD

**Responsabilidades:**
- Validar requests (usando FormRequest)
- Llamar al Service correspondiente
- Retornar respuestas HTTP (JSON o Inertia)
- Manejo de errores HTTP

**Ejemplo:**
```php
public function store(ProductRequest $request): JsonResponse
{
    try {
        $product = $this->productService->createProductForStore(
            $this->validateStore(),
            $request->user(),
            $request->validated()
        );
        
        return $this->success($product, 'Product created', 201);
    } catch (\Exception $e) {
        return $this->error('Failed to create product', 500);
    }
}
```

---

### **2. Service (Capa de Lógica de Negocio)**
- ✅ **Toda la lógica de negocio**
- ✅ Orquestación de operaciones
- ✅ Transacciones de BD
- ❌ NO debe tener consultas directas a BD

**Responsabilidades:**
- Validaciones de negocio
- Orquestar múltiples repositorios
- Manejar transacciones (DB::transaction)
- Transformar datos
- Aplicar reglas de negocio

**Ejemplo:**
```php
public function createProductForStore(Store $store, User $user, array $data): Model
{
    return DB::transaction(function () use ($store, $user, $data) {
        $data['store_id'] = $store->id;
        $data['created_by'] = $user->id;

        $categoryIds = $data['category_ids'] ?? [];
        unset($data['category_ids']);

        $product = $this->productRepository->create($data);

        if (!empty($categoryIds)) {
            $this->productRepository->syncCategories($product, $categoryIds);
        }

        return $product;
    });
}
```

---

### **3. Repository (Capa de Acceso a Datos)**
- ✅ **Todas las consultas a BD**
- ✅ Hereda de BaseRepository
- ✅ Solo métodos complejos (joins, aggregations, etc.)

**Responsabilidades:**
- Todas las consultas Eloquent
- Joins complejos
- Agregaciones y cálculos
- Relaciones específicas
- Scopes personalizados

**NO crear métodos wrapper:**
```php
// ❌ MAL - Método innecesario
public function getByStatus(string $status): Collection
{
    return $this->getBy(['status' => $status]);
}

// ✅ BIEN - Usar directamente desde Service
$products = $this->productRepository->getBy(['status' => 'active']);
```

**SÍ crear métodos específicos:**
```php
// ✅ BIEN - Lógica compleja
public function getProductsWithLowStock(string $storeId, int $threshold): Collection
{
    return $this->model
        ->where('store_id', $storeId)
        ->where('manage_stock', true)
        ->where('stock_quantity', '<=', $threshold)
        ->with(['images', 'variants'])
        ->orderBy('stock_quantity', 'asc')
        ->get();
}
```

---

## 🔧 Métodos Genéricos del BaseRepository

**Usar directamente desde Services:**

```php
// Obtener registros
$this->repository->getBy(['status' => 'active']);
$this->repository->getFirstBy(['id' => $id]);
$this->repository->getById($id);
$this->repository->getAll();

// Crear/Actualizar/Eliminar
$this->repository->create($data);
$this->repository->updateBy(['id' => $id], $data);
$this->repository->deleteBy(['id' => $id]);

// Utilidades
$this->repository->existsBy(['email' => $email]);
$this->repository->count(['status' => 'pending']);
$this->repository->paginateWithFilters($filters);
```

---

## 📦 Estructura de Directorios

```
app/
├── Http/
│   ├── Controllers/          # Solo validación + HTTP
│   │   ├── ProductController.php
│   │   ├── ProductVariantController.php
│   │   └── ProductReviewController.php
│   └── Requests/            # Validaciones
│       ├── ProductRequest.php
│       └── ProductImageRequest.php
├── Services/                # Lógica de negocio
│   ├── ProductService.php
│   ├── ProductVariantService.php
│   └── ProductReviewService.php
├── Repositories/            # Consultas a BD
│   ├── BaseRepository.php
│   └── Product/
│       ├── ProductRepository.php
│       ├── ProductVariantRepository.php
│       └── ProductReviewRepository.php
└── Models/                  # Eloquent Models
    ├── Product.php
    ├── ProductVariant.php
    └── ProductReview.php
```

---

## ✅ Checklist de Implementación

### **Al crear un Controller:**
- [ ] Inyectar solo el Service necesario
- [ ] Usar FormRequest para validación
- [ ] Solo llamadas a Service
- [ ] Retornar respuestas HTTP limpias
- [ ] Try-catch para manejo de errores

### **Al crear un Service:**
- [ ] Inyectar Repositories necesarios
- [ ] Usar DB::transaction cuando sea necesario
- [ ] NO hacer consultas directas (usar Repository)
- [ ] Aplicar lógica de negocio
- [ ] Retornar datos procesados

### **Al crear un Repository:**
- [ ] Extender BaseRepository
- [ ] Solo métodos con lógica compleja
- [ ] Usar métodos genéricos del BaseRepository
- [ ] NO crear wrappers innecesarios
- [ ] Documentar métodos complejos

---

## 🚫 Anti-Patrones a Evitar

### ❌ Controller con lógica de negocio
```php
// MAL
public function store(Request $request)
{
    $product = Product::create($request->all());
    $product->categories()->sync($request->category_ids);
    return response()->json($product);
}
```

### ❌ Service con consultas directas
```php
// MAL
public function getProducts()
{
    return Product::where('status', 'active')->get();
}
```

### ❌ Repository con wrappers innecesarios
```php
// MAL
public function findById($id)
{
    return $this->getById($id); // Ya existe en BaseRepository
}
```

---

## ✅ Patrones Correctos

### ✅ Controller limpio
```php
public function store(ProductRequest $request): JsonResponse
{
    $product = $this->productService->createProduct($request->validated());
    return $this->success($product, 'Created', 201);
}
```

### ✅ Service con lógica de negocio
```php
public function createProduct(array $data): Product
{
    return DB::transaction(function () use ($data) {
        $product = $this->productRepository->create($data);
        $this->productRepository->syncCategories($product, $data['categories']);
        return $product;
    });
}
```

### ✅ Repository con método específico
```php
public function getProductsWithCategories(string $storeId): Collection
{
    return $this->model
        ->where('store_id', $storeId)
        ->with(['categories', 'images'])
        ->get();
}
```

---

## 📚 Principios Adicionales

### **1. JSON en BD**
- ✅ Solo para: configuraciones simples, metadatos opcionales
- ❌ NO para: datos estructurados, información repetitiva

### **2. Inyección de Dependencias**
- Siempre usar constructor injection
- Nunca usar facades en Services/Repositories (excepto DB, Cache)

### **3. Transacciones**
- Usar DB::transaction para operaciones atómicas
- Manejar en Service, no en Controller ni Repository

### **4. Validación**
- FormRequest para validación de entrada
- Service para validación de negocio
- Repository para validación de datos

---

**Última actualización:** 2026-03-29
