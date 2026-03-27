---
description: SPA Admin Pattern - Single Page with Modals/Sliders (Fase 4)
---

# SPA Admin Pattern: Modals & Sliders

Estándar para interfaces de administración con operaciones CRUD en una sola página.

## Principio

> **Una página de administración = Todas las operaciones CRUD en modals/sliders**

**❌ Antes:** Páginas separadas para Index, Create, Edit, Show  
**✅ Ahora:** Una página `Index.vue` con modals/sliders para cada acción

## Estructura recomendada

```
modules/products/
├── pages/
│   └── Index.vue          # Página única con tabla + modals
├── components/
│   ├── ProductTable.vue   # Tabla de listado
│   ├── ProductModal.vue   # Modal para Create/Edit
│   ├── ProductView.vue    # Slider/Modal para Show
│   └── ProductFilters.vue # Filtros de la tabla
└── composables/
    └── useProductManager.js # Lógica de estado y operaciones
```

## Componentes de UI

### Modal (Create/Edit)
```vue
<!-- ProductModal.vue -->
<template>
  <Teleport to="body">
    <div v-if="isOpen" class="modal-overlay" @click="close">
      <div class="modal-container" @click.stop>
        <header class="modal-header">
          <h3>{{ isEditing ? 'Edit Product' : 'Add Product' }}</h3>
          <button @click="close">×</button>
        </header>
        <form @submit.prevent="save">
          <!-- Form fields -->
        </form>
      </div>
    </div>
  </Teleport>
</template>
```

### Slider (Show/Preview)
```vue
<!-- ProductView.vue -->
<template>
  <Transition name="slide">
    <div v-if="isOpen" class="slider-panel">
      <header class="slider-header">
        <h3>Product Details</h3>
        <button @click="close">×</button>
      </header>
      <div class="slider-content">
        <!-- View content -->
      </div>
    </div>
  </Transition>
</template>
```

## Composable reutilizable

```javascript
// useEntityManager.js
export function useEntityManager(entityName) {
  const items = ref([])
  const selectedItem = ref(null)
  const isModalOpen = ref(false)
  const isViewOpen = ref(false)
  const isEditing = ref(false)

  const openCreate = () => {
    selectedItem.value = null
    isEditing.value = false
    isModalOpen.value = true
  }

  const openEdit = (item) => {
    selectedItem.value = { ...item }
    isEditing.value = true
    isModalOpen.value = true
  }

  const openView = (item) => {
    selectedItem.value = item
    isViewOpen.value = true
  }

  const save = async (data) => {
    if (isEditing.value) {
      await update(data)
    } else {
      await create(data)
    }
    isModalOpen.value = false
  }

  return {
    items,
    selectedItem,
    isModalOpen,
    isViewOpen,
    isEditing,
    openCreate,
    openEdit,
    openView,
    save
  }
}
```

## Page principal

```vue
<!-- Index.vue -->
<template>
  <BusinessLayout>
    <div class="space-y-6">
      <header class="flex justify-between items-center">
        <h1>Products</h1>
        <button @click="openCreate" class="btn-primary">
          + Add Product
        </button>
      </header>

      <ProductTable 
        :items="items"
        @edit="openEdit"
        @view="openView"
        @delete="confirmDelete"
      />

      <ProductModal
        :is-open="isModalOpen"
        :is-editing="isEditing"
        :item="selectedItem"
        @save="save"
        @close="isModalOpen = false"
      />

      <ProductView
        :is-open="isViewOpen"
        :item="selectedItem"
        @close="isViewOpen = false"
      />
    </div>
  </BusinessLayout>
</template>
```

## Ventajas

- **UX fluido:** No hay recarga de página entre operaciones
- **Estado preservado:** Filtros y paginación se mantienen
- **Menos código:** Sin rutas y páginas duplicadas
- **Móvil-friendly:** Sliders funcionan mejor en móviles

## Checklist de implementación

- [ ] Crear `useEntityManager` composable reutilizable
- [ ] Crear componentes `EntityModal.vue` y `EntityView.vue`
- [ ] Consolidar `Create.vue`, `Edit.vue`, `Show.vue` en `Index.vue`
- [ ] Actualizar rutas para usar solo `Index`
- [ ] Implementar teleports para modals
- [ ] Agregar transiciones para sliders
