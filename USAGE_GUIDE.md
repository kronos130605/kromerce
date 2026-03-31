# 📖 GUÍA DE USO - SISTEMA DE PRODUCTOS

## 🚀 INICIO RÁPIDO

### **1. Ver la Lista de Productos**

Navega a `/products` y verás:
- **Barra de búsqueda** - Busca productos en tiempo real (debounce 300ms)
- **Botón de filtros** - Muestra/oculta el panel de filtros avanzados
- **Quick filters** - Botones rápidos para Active/Draft
- **Tabla de productos** - Con selección múltiple
- **Estadísticas** - 4 tarjetas con métricas clave

---

## 🔍 FILTROS AVANZADOS

### **Activar Filtros**
Click en el botón "Filters" en la barra superior. El panel se desplegará con animación.

### **Filtros Disponibles**

**1. Category** - Filtrar por categoría
```
Dropdown con todas las categorías disponibles
```

**2. Status** - Filtrar por estado
```
- All Statuses
- Active
- Inactive
- Draft
```

**3. Stock Status** - Filtrar por stock
```
- All Stock
- In Stock
- Low Stock
- Out of Stock
```

**4. Featured** - Productos destacados
```
- All Products
- Featured Only
- Not Featured
```

**5. Price Range** - Rango de precios
```
Min Price: $0.00
Max Price: $999.99
```

### **Limpiar Filtros**
Click en "Clear All" dentro del panel de filtros.

---

## ✅ SELECCIÓN MÚLTIPLE Y BULK ACTIONS

### **Seleccionar Productos**

**Método 1: Checkbox Individual**
- Click en el checkbox al inicio de cada fila

**Método 2: Seleccionar Todos**
- Click en el checkbox del header de la tabla

### **Barra de Acciones Masivas**

Cuando seleccionas productos, aparece una barra flotante en la parte inferior con:

**Información:**
- Cantidad de productos seleccionados
- Botón "Clear selection"

**Acciones Rápidas:**

1. **Activate** (✓) - Activar productos seleccionados
2. **Deactivate** (⏸) - Desactivar productos
3. **Draft** (✏️) - Mover a borrador
4. **Export** (📥) - Exportar a CSV
5. **Delete** (🗑️) - Eliminar productos (con confirmación)

---

## 📝 CREAR/EDITAR PRODUCTOS

### **Crear Nuevo Producto**

1. Click en botón "Add Product" (esquina superior derecha)
2. Se abre modal con wizard de 4 pasos:
   - **Basic Info** - Nombre, descripción, SKU
   - **Pricing** - Precios, costos, moneda
   - **Media** - Imágenes del producto
   - **SEO** - Meta tags y optimización

3. Navega entre pasos con botones "Next" / "Previous"
4. Click en "Save" para crear el producto

### **Editar Producto Existente**

**Método 1: Desde la tabla**
- Click en el icono de editar (✏️) en la fila del producto

**Método 2: Desde vista detalle**
- Click en ver (👁️) y luego "Edit" en el modal

---

## 🎨 GESTIÓN DE VARIANTES

### **Acceder a Variantes**

1. Edita un producto
2. Navega al paso de variantes (si está habilitado)
3. O accede directamente desde el detalle del producto

### **Crear Variante**

1. Click en "Add Variant"
2. Completa el formulario:
   - **SKU** - Código único de la variante
   - **Name** - Nombre descriptivo (ej: "Large / Red")
   - **Price** - Precio base
   - **Sale Price** - Precio de oferta (opcional)
   - **Stock Quantity** - Cantidad en inventario
   - **Weight** - Peso en kg
   - **Attributes** - Selecciona valores de atributos

3. Opciones:
   - ☑️ Set as default variant
   - ☑️ Active

4. Click "Save Variant"

### **Gestionar Variantes**

**Ver Estadísticas:**
- Total Variants
- Total Stock
- Price Range

**Acciones por Variante:**
- ⭐ Set as Default
- ✏️ Edit
- 🗑️ Delete

---

## ⭐ SISTEMA DE REVIEWS

### **Ver Reviews de un Producto**

```javascript
GET /products/{product}/reviews
```

**Filtros disponibles:**
- Status (pending, approved, rejected)
- Rating (1-5 estrellas)
- Verified Purchase

### **Crear Review**

```javascript
POST /products/{product}/reviews
{
    "rating": 5,
    "title": "Excellent product!",
    "comment": "Very satisfied with the quality...",
    "verified_purchase": true
}
```

### **Moderar Review**

```javascript
POST /products/{product}/reviews/{review}/moderate
{
    "status": "approved",
    "moderation_notes": "Approved - meets guidelines"
}
```

### **Votar Review**

```javascript
POST /products/{product}/reviews/{review}/vote
{
    "is_helpful": true
}
```

---

## ❓ SISTEMA DE Q&A

### **Ver Preguntas**

```javascript
GET /products/{product}/questions
```

**Filtros:**
- Status
- Has Answer (true/false)

### **Crear Pregunta**

```javascript
POST /products/{product}/questions
{
    "question": "What is the warranty period?"
}
```

### **Responder Pregunta**

```javascript
POST /products/{product}/questions/{question}/answers
{
    "answer": "This product has a 2-year warranty."
}
```

### **Votar Respuesta**

```javascript
POST /products/{product}/questions/{question}/answers/{answer}/vote
{
    "is_helpful": true
}
```

---

## 🔧 COMPONENTES REUTILIZABLES

### **PriceInput**

```vue
<PriceInput
    v-model="price"
    currency="USD"
    label="Product Price"
    :min="0"
    :max="10000"
    required
    :error="errors.price"
/>
```

**Props:**
- `modelValue` - Valor del precio
- `currency` - Código de moneda (USD, EUR, etc.)
- `label` - Etiqueta del campo
- `min` / `max` - Límites de precio
- `error` - Mensaje de error
- `showCurrency` - Mostrar símbolo y código

---

### **StatusBadge**

```vue
<StatusBadge 
    status="active" 
    type="product" 
    size="md"
    dot
/>
```

**Props:**
- `status` - Estado del producto
- `type` - Tipo de badge (product, order, payment, shipping, user)
- `size` - Tamaño (sm, md, lg)
- `dot` - Mostrar punto en lugar de icono

**Estados soportados:**
- Product: active, inactive, draft, archived, pending, approved, rejected
- Order: pending, processing, completed, cancelled, refunded
- Payment: paid, unpaid, partial, refunded, failed
- Shipping: pending, shipped, in_transit, delivered, returned

---

### **StockIndicator**

```vue
<StockIndicator 
    :stock="25" 
    :low-stock-threshold="10"
    variant="badge"
    size="md"
/>
```

**Props:**
- `stock` - Cantidad en stock
- `lowStockThreshold` - Umbral de stock bajo
- `variant` - Estilo (badge, text, bar)
- `size` - Tamaño (sm, md, lg)
- `showLabel` - Mostrar etiqueta
- `showIcon` - Mostrar icono

**Estados automáticos:**
- Out of Stock (0) - Rojo
- Low Stock (≤ threshold) - Amarillo
- In Stock (> threshold) - Verde

---

### **CurrencySelector**

```vue
<CurrencySelector
    v-model="currency"
    label="Currency"
    :show-flag="true"
    :show-symbol="true"
/>
```

**Monedas incluidas:**
USD, EUR, GBP, JPY, CAD, AUD, CHF, CNY, MXN, BRL, INR, KRW

---

## 🎯 COMPOSABLES

### **useProductFilters**

```javascript
import { useProductFilters } from '@/modules/products/composables/useProductFilters.js';

const {
    filters,
    showFilters,
    hasActiveFilters,
    activeFilterCount,
    updateFilter,
    clearFilters,
    toggleFilters,
    searchProducts,
    setPriceRange,
    quickFilters
} = useProductFilters(initialFilters);

// Usar
searchProducts('laptop');
updateFilter('status', 'active');
setPriceRange(100, 500);
quickFilters.active();
```

---

### **useBulkActions**

```javascript
import { useBulkActions } from '@/modules/products/composables/useBulkActions.js';

const {
    selectedItems,
    isProcessing,
    hasSelection,
    selectionCount,
    toggleItem,
    toggleAll,
    clearSelection,
    bulkUpdateStatus,
    bulkDelete,
    quickActions
} = useBulkActions();

// Usar
toggleItem('product-id-123');
await quickActions.activate();
await bulkDelete();
```

---

### **useVariants**

```javascript
import { useVariants } from '@/modules/products/composables/useVariants.js';

const {
    variants,
    loading,
    hasVariants,
    totalStock,
    lowestPrice,
    highestPrice,
    fetchVariants,
    createVariant,
    updateVariant,
    deleteVariant,
    setDefaultVariant
} = useVariants(productId);

// Usar
await fetchVariants();
await createVariant(variantData);
await setDefaultVariant(variantId);
```

---

## 🎨 PERSONALIZACIÓN

### **Colores del Tema**

Los componentes usan las clases de Tailwind:
- Primary: `blue-600`
- Success: `green-600`
- Warning: `yellow-600`
- Danger: `red-600`

### **Dark Mode**

Todos los componentes soportan dark mode automáticamente usando:
```css
dark:bg-gray-800
dark:text-white
dark:border-gray-700
```

---

## 🐛 TROUBLESHOOTING

### **Los filtros no funcionan**

1. Verifica que las rutas estén configuradas correctamente
2. Asegúrate de que el backend retorne los filtros en `props.filters`
3. Revisa la consola del navegador para errores

### **Bulk actions no responden**

1. Verifica que los endpoints de bulk estén configurados:
   - `/products/bulk/status`
   - `/products/bulk/delete`
   - etc.

2. Asegúrate de que el ProductService tenga los métodos bulk

### **Las variantes no se cargan**

1. Verifica la ruta: `GET /products/{product}/variants`
2. Asegúrate de que el ProductVariantController esté registrado
3. Revisa que el producto tenga el ID correcto

---

## 📚 RECURSOS ADICIONALES

- **ARCHITECTURE.md** - Patrón 3-tier completo
- **FRONTEND_ANALYSIS.md** - Análisis del frontend
- **IMPLEMENTATION_COMPLETE.md** - Resumen de implementación
- **API Documentation** - (Pendiente de crear)

---

**Última actualización:** 2026-03-29  
**Versión:** 1.0.0
