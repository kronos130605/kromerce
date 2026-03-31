coe# 🛍️ PROPUESTA: STOREFRONT PARA CLIENTES

## 📋 ANÁLISIS DE REFERENCIA

### **AliExpress / Made-in-China - Características Clave**

**Estructura:**
1. **Marketplace Global** - Todos los productos de todas las tiendas
2. **Tienda Individual** - Productos de una tienda específica
3. **Categorías** - Navegación por categorías
4. **Búsqueda Global** - Buscar en todo el marketplace
5. **Filtros Avanzados** - Precio, rating, envío, etc.

---

## 🎯 PROPUESTA DE ARQUITECTURA

### **1. MARKETPLACE (Vista Global)**

**URL:** `/` o `/marketplace`

**Secciones:**
```
┌─────────────────────────────────────────┐
│  HEADER                                  │
│  - Logo                                  │
│  - Search Bar (global)                   │
│  - Categories Menu                       │
│  - Cart, Account                         │
├─────────────────────────────────────────┤
│  HERO BANNER                             │
│  - Promociones destacadas                │
│  - Call to action                        │
├─────────────────────────────────────────┤
│  FEATURED CATEGORIES                     │
│  - Grid de categorías principales        │
├─────────────────────────────────────────┤
│  TRENDING PRODUCTS                       │
│  - Productos más vendidos (todas tiendas)│
├─────────────────────────────────────────┤
│  NEW ARRIVALS                            │
│  - Productos nuevos (últimos 30 días)    │
├─────────────────────────────────────────┤
│  TOP STORES                              │
│  - Tiendas destacadas con mejores ratings│
├─────────────────────────────────────────┤
│  DEALS OF THE DAY                        │
│  - Ofertas del día                       │
├─────────────────────────────────────────┤
│  BROWSE BY CATEGORY                      │
│  - Secciones por categoría               │
└─────────────────────────────────────────┘
```

---

### **2. STORE PAGE (Vista Individual)**

**URL:** `/stores/{store-slug}` o `/{store-slug}`

**Secciones:**
```
┌─────────────────────────────────────────┐
│  STORE HEADER                            │
│  - Logo de la tienda                     │
│  - Nombre y descripción                  │
│  - Rating y reviews                      │
│  - Botón "Follow Store"                  │
│  - Stats (productos, ventas, etc.)       │
├─────────────────────────────────────────┤
│  STORE NAVIGATION                        │
│  - Home | Products | About | Contact    │
├─────────────────────────────────────────┤
│  STORE BANNER                            │
│  - Banner personalizado de la tienda     │
├─────────────────────────────────────────┤
│  FEATURED PRODUCTS (de esta tienda)     │
│  - Productos destacados                  │
├─────────────────────────────────────────┤
│  ALL PRODUCTS                            │
│  - Grid de productos con filtros         │
│  - Sidebar: Categories, Price, etc.      │
├─────────────────────────────────────────┤
│  STORE INFO                              │
│  - Sobre la tienda                       │
│  - Políticas de envío                    │
│  - Métodos de pago                       │
└─────────────────────────────────────────┘
```

---

### **3. PRODUCT LISTING PAGE**

**URL:** `/products` o `/category/{category-slug}`

**Layout:**
```
┌────────────┬────────────────────────────┐
│  SIDEBAR   │  PRODUCTS GRID             │
│            │                            │
│  Filters:  │  ┌────┐ ┌────┐ ┌────┐    │
│  □ Category│  │Prod│ │Prod│ │Prod│    │
│  □ Price   │  └────┘ └────┘ └────┘    │
│  □ Rating  │                            │
│  □ Shipping│  ┌────┐ ┌────┐ ┌────┐    │
│  □ Store   │  │Prod│ │Prod│ │Prod│    │
│            │  └────┘ └────┘ └────┘    │
│            │                            │
│            │  Pagination                │
└────────────┴────────────────────────────┘
```

---

### **4. PRODUCT DETAIL PAGE**

**URL:** `/products/{product-slug}` o `/p/{product-slug}`

**Layout:**
```
┌─────────────────────────────────────────┐
│  BREADCRUMB                              │
│  Home > Category > Product               │
├──────────────┬──────────────────────────┤
│  IMAGES      │  PRODUCT INFO            │
│  Gallery     │  - Title                 │
│  Thumbnails  │  - Price                 │
│              │  - Rating & Reviews      │
│              │  - Variants (size, color)│
│              │  - Quantity selector     │
│              │  - Add to Cart           │
│              │  - Buy Now               │
│              │  - Store info            │
├──────────────┴──────────────────────────┤
│  TABS                                    │
│  - Description                           │
│  - Specifications                        │
│  - Reviews                               │
│  - Q&A                                   │
├─────────────────────────────────────────┤
│  RELATED PRODUCTS                        │
│  - De la misma tienda                    │
│  - De la misma categoría                 │
└─────────────────────────────────────────┘
```

---

## 🎨 COMPONENTES A CREAR

### **Layout Components**
```
resources/js/layouts/
├── StorefrontLayout.vue          # Layout principal para clientes
├── StoreLayout.vue                # Layout para páginas de tienda
└── CheckoutLayout.vue             # Layout para checkout
```

### **Page Components**
```
resources/js/pages/storefront/
├── Home.vue                       # Marketplace home
├── ProductListing.vue             # Lista de productos
├── ProductDetail.vue              # Detalle de producto
├── StoreHome.vue                  # Home de tienda individual
├── StoreProducts.vue              # Productos de tienda
└── StoreAbout.vue                 # Sobre la tienda
```

### **Feature Components**
```
resources/js/components/storefront/
├── ProductCard.vue                # Card de producto
├── ProductGrid.vue                # Grid de productos
├── ProductFilters.vue             # Filtros de productos
├── StoreCard.vue                  # Card de tienda
├── CategoryCard.vue               # Card de categoría
├── HeroBanner.vue                 # Banner hero
├── FeaturedSection.vue            # Sección destacada
├── ProductGallery.vue             # Galería de imágenes
├── ProductVariants.vue            # Selector de variantes
├── AddToCart.vue                  # Botón agregar al carrito
├── ReviewList.vue                 # Lista de reviews
├── QuestionList.vue               # Lista de Q&A
└── StoreHeader.vue                # Header de tienda
```

### **Shared Components**
```
resources/js/components/storefront/shared/
├── Breadcrumb.vue                 # Breadcrumbs
├── Pagination.vue                 # Paginación
├── RatingStars.vue                # Estrellas de rating
├── PriceDisplay.vue               # Display de precio
├── SearchBar.vue                  # Barra de búsqueda
└── CategoryMenu.vue               # Menú de categorías
```

---

## 🗂️ ESTRUCTURA DE RUTAS

### **Frontend Routes (Inertia)**

```php
// routes/web.php

// MARKETPLACE
Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/products', [StorefrontController::class, 'products'])->name('products.index');
Route::get('/products/{product:slug}', [StorefrontController::class, 'productDetail'])->name('products.show');
Route::get('/category/{category:slug}', [StorefrontController::class, 'category'])->name('category.show');
Route::get('/search', [StorefrontController::class, 'search'])->name('search');

// STORES
Route::get('/stores', [StorefrontController::class, 'stores'])->name('stores.index');
Route::get('/stores/{store:slug}', [StoreController::class, 'home'])->name('store.home');
Route::get('/stores/{store:slug}/products', [StoreController::class, 'products'])->name('store.products');
Route::get('/stores/{store:slug}/about', [StoreController::class, 'about'])->name('store.about');

// ALTERNATIVE: Custom domain per store
// shop1.kromerce.com -> StoreController@home
```

---

## 🎯 FUNCIONALIDADES CLAVE

### **1. Búsqueda Global**
- Buscar en todos los productos de todas las tiendas
- Autocompletado
- Búsqueda por categoría
- Filtros avanzados

### **2. Filtros de Productos**
- **Precio:** Min-Max slider
- **Categoría:** Checkbox múltiple
- **Rating:** 4+ estrellas, 3+, etc.
- **Tienda:** Filtrar por tienda
- **Envío:** Envío gratis, envío rápido
- **Disponibilidad:** En stock, pre-orden

### **3. Ordenamiento**
- Relevancia
- Precio: Menor a Mayor
- Precio: Mayor a Menor
- Más vendidos
- Mejor calificados
- Nuevos

### **4. Product Card**
```vue
<ProductCard>
  - Imagen del producto
  - Nombre
  - Precio (con descuento si aplica)
  - Rating (estrellas + cantidad de reviews)
  - Badge: "Best Seller", "New", "Sale"
  - Store name (pequeño)
  - Botón "Quick View"
  - Botón "Add to Cart"
</ProductCard>
```

### **5. Store Card**
```vue
<StoreCard>
  - Logo de la tienda
  - Nombre
  - Rating
  - Cantidad de productos
  - Badge: "Verified", "Top Seller"
  - Botón "Visit Store"
</StoreCard>
```

---

## 🎨 DISEÑO VISUAL

### **Paleta de Colores**
```css
Primary: #2563eb (blue-600)
Secondary: #7c3aed (purple-600)
Success: #10b981 (green-500)
Warning: #f59e0b (amber-500)
Danger: #ef4444 (red-500)
Gray: #6b7280 (gray-500)
```

### **Tipografía**
```css
Headings: font-bold
Body: font-normal
Small: text-sm
Tiny: text-xs
```

### **Espaciado**
```css
Container: max-w-7xl mx-auto px-4
Gap: gap-4, gap-6, gap-8
Padding: p-4, p-6, p-8
```

---

## 📊 DATOS NECESARIOS DEL BACKEND

### **Marketplace Home**
```php
return Inertia::render('storefront/Home', [
    'hero_banners' => $banners,
    'featured_categories' => $categories,
    'trending_products' => $trendingProducts,
    'new_arrivals' => $newProducts,
    'top_stores' => $topStores,
    'deals' => $dealsOfTheDay,
]);
```

### **Product Listing**
```php
return Inertia::render('storefront/ProductListing', [
    'products' => $products->paginate(24),
    'filters' => $activeFilters,
    'categories' => $categories,
    'price_range' => ['min' => 0, 'max' => 10000],
]);
```

### **Product Detail**
```php
return Inertia::render('storefront/ProductDetail', [
    'product' => $product->load([
        'images',
        'variants',
        'categories',
        'store',
        'reviews' => fn($q) => $q->approved()->latest()->limit(10)
    ]),
    'related_products' => $relatedProducts,
    'store_products' => $storeProducts,
]);
```

### **Store Home**
```php
return Inertia::render('storefront/StoreHome', [
    'store' => $store->load(['owner', 'badges']),
    'featured_products' => $featuredProducts,
    'all_products' => $allProducts->paginate(24),
    'categories' => $storeCategories,
    'stats' => [
        'total_products' => $store->products()->count(),
        'rating' => $store->average_rating,
        'total_reviews' => $store->reviews_count,
    ],
]);
```

---

## 🚀 PLAN DE IMPLEMENTACIÓN

### **FASE 1: Fundación (2-3 horas)**
1. ✅ Crear StorefrontLayout
2. ✅ Crear componentes base (ProductCard, StoreCard, etc.)
3. ✅ Crear rutas básicas
4. ✅ Crear StorefrontController

### **FASE 2: Marketplace Home (2-3 horas)**
1. ✅ Hero Banner
2. ✅ Featured Categories
3. ✅ Trending Products
4. ✅ New Arrivals
5. ✅ Top Stores

### **FASE 3: Product Listing (2-3 horas)**
1. ✅ Product Grid
2. ✅ Filters Sidebar
3. ✅ Sorting
4. ✅ Pagination

### **FASE 4: Product Detail (2-3 horas)**
1. ✅ Product Gallery
2. ✅ Product Info
3. ✅ Variants Selector
4. ✅ Add to Cart
5. ✅ Reviews & Q&A

### **FASE 5: Store Pages (2-3 horas)**
1. ✅ Store Header
2. ✅ Store Home
3. ✅ Store Products
4. ✅ Store About

---

## 🎯 DECISIONES CLAVE

### **1. Routing Strategy**

**Opción A: Subdirectorio (Recomendado)**
```
kromerce.com/                    → Marketplace
kromerce.com/stores/shop1        → Tienda individual
kromerce.com/products/laptop     → Producto
```

**Opción B: Subdominio**
```
kromerce.com/                    → Marketplace
shop1.kromerce.com/              → Tienda individual
```

**Recomendación:** Opción A (más simple, mejor SEO)

---

### **2. Product URL Structure**

**Opción A: Global (Recomendado)**
```
/products/{product-slug}
```

**Opción B: Por tienda**
```
/stores/{store-slug}/products/{product-slug}
```

**Recomendación:** Opción A (mejor para marketplace)

---

### **3. Search Strategy**

**Global Search:**
- Buscar en todos los productos
- Filtrar por tienda después

**Store Search:**
- Buscar solo en productos de la tienda
- Más rápido y relevante

---

## 📝 PRÓXIMOS PASOS

1. **Revisar y aprobar propuesta**
2. **Definir prioridades**
3. **Crear wireframes detallados (opcional)**
4. **Comenzar implementación por fases**

---

## 🤔 PREGUNTAS PARA TI

1. **¿Prefieres empezar con Marketplace o Store pages?**
2. **¿Quieres custom domains por tienda (shop1.kromerce.com)?**
3. **¿Necesitas carrito de compras en esta fase o después?**
4. **¿Alguna funcionalidad específica de AliExpress que quieras incluir?**

---

**Fecha:** 2026-03-29  
**Estado:** 📋 PROPUESTA  
**Siguiente paso:** Aprobación y priorización
