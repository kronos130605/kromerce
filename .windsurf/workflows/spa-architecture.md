---
description: SPA Architecture Workflow - Define the standard architecture for Single Page Applications with dynamic content
---

# SPA Architecture Workflow

Este workflow define la arquitectura estándar para crear aplicaciones de una sola página (SPA) con contenido dinámico en el proyecto Kromerce.

## 🏗️ **Arquitectura Estándar**

### **1. Estructura de Archivos**

```
resources/js/
├── layouts/
│   └── [moduleName]Layout.vue          # Layout principal con header + sidebar + slot
├── modules/
│   └── [moduleName]/
│       ├── pages/
│       │   └── Index.vue                # Página de entrada del SPA
│       └── content/
│           ├── [section]Content.vue     # Contenido dinámico específico
│           └── [section]Content.vue
├── composables/
│   └── use[feature].js              # Lógica reutilizable (ej: useSidebar)
└── components/
    └── (componentes específicos del módulo)
```

### **2. Flujo de Datos**

```
Controller → modules/[moduleName]/pages/Index.vue → Layout.vue → ContentComponent.vue
     ↓              ↓                ↓
  activeTab    slot dinámico    contenido específico
```

### **3. Responsabilidades**

**Controller:**
- Renderiza `[moduleName]/Index` con `activeTab` apropiado
- Pasa los datos necesarios como props

**modules/[moduleName]/pages/Index.vue:**
- Importa y usa el Layout correspondiente
- No contiene lógica de negocio, solo estructura

**Layout.vue:**
- Contiene header, sidebar y estructura visual
- Detecta `activeTab` de las props
- Renderiza el componente de contenido dinámicamente
- Usa composables para estado compartido

**ContentComponent.vue:**
- Contiene el contenido específico de la sección
- Recibe props del controller
- Es completamente independiente

### **4. Archivos Legacy (No recomendados)**

Los archivos en `modules/[moduleName]/pages/[Section].vue` son considerados legacy:
- Se mantienen por compatibilidad
- No se deben crear nuevos archivos aquí
- Preferir usar la estructura SPA en `Pages/[ModuleName]/Content/`

## 📋 **Pasos para Crear Nuevo SPA**

### **Paso 1: Crear Layout**
```bash
# Crear layout principal
touch resources/js/layouts/[moduleName]Layout.vue
```

**Estructura del Layout:**
```vue
<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header/Navbar -->
    <[moduleName]Navbar ref="navbarRef" />

    <div class="flex pt-16">
      <!-- Sidebar -->
      <[moduleName]Sidebar ref="sidebarRef" />

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto">
        <div class="px-6 pb-6">
          <component 
            :is="currentContent" 
            :[propName]="page.props.[propName]"
          />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Importar componentes de contenido
import [section1]Content from '@/modules/[moduleName]/content/[section1]Content.vue';
import [section2]Content from '@/modules/[moduleName]/content/[section2]Content.vue';

const page = usePage();

// Detectar active tab
const activeTab = computed(() => page.props.activeTab || 'default');

// Mapeo de componentes
const contentComponents = {
    [section1]: [section1]Content,
    [section2]: [section2]Content,
};

// Componente actual
const currentContent = computed(() => {
    return contentComponents[activeTab.value] || [DefaultContent];
});
</script>
```

### **Paso 2: Crear Página de Entrada**
```bash
# Crear página de entrada
touch resources/js/modules/[moduleName]/pages/Index.vue
```

**Estructura de Index.vue:**
```vue
<script setup>
import [moduleName]Layout from '@/layouts/[moduleName]Layout.vue';
</script>

<template>
    <[moduleName]Layout />
</template>
```

### **Paso 3: Crear Componentes de Contenido**
```bash
# Crear directorio de contenido
mkdir -p resources/js/modules/[moduleName]/content

# Crear componentes de contenido
touch resources/js/modules/[moduleName]/content/[section1]Content.vue
touch resources/js/modules/[moduleName]/content/[section2]Content.vue
```

**Estructura del ContentComponent:**
```vue
<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

// Props específicos del contenido
const [propName] = computed(() => page.props.[propName] || {});
</script>

<template>
  <div class="space-y-8">
    <!-- Contenido específico de la sección -->
    <h1>{{ t('[section].title') }}</h1>
    <!-- ... más contenido ... -->
  </div>
</template>
```

### **Paso 4: Actualizar Controller**
```php
// En el Controller correspondiente
return Inertia::render('[moduleName]/Index', [
    'activeTab' => '[section]',
    '[propName]' => $data,
    // ... otras props
]);
```

### **Paso 5: Configurar Resolución de Rutas**
```javascript
// En resources/js/app.js
// Handle [ModuleName] routes
if (baseName.startsWith('[ModuleName]/')) {
    const [moduleName]Page = baseName.replace('[ModuleName]/', '');
    const pagePath = `./modules/${moduleName}/pages/${[moduleName]Page}.vue`;
    
    return resolvePageComponent(
        pagePath,
        import.meta.glob('./modules/**/*.vue'),
    );
}

// Handle [ModuleName] Index (main SPA page)
if (baseName === '[ModuleName]/Index') {
    const pagePath = `./modules/${moduleName}/pages/Index.vue`;
    
    return resolvePageComponent(
        pagePath,
        import.meta.glob('./modules/**/*.vue'),
    );
}
```

## 🎯 **Ejemplo Completo: Business SPA**

### **Archivos Creados:**
- `layouts/BusinessLayout.vue` ✅
- `modules/business/pages/Index.vue` ✅
- `modules/business/content/DashboardContent.vue` ✅
- `modules/business/content/ProductsContent.vue` ✅
- `modules/business/content/OrdersContent.vue` ✅
- `modules/business/content/AnalyticsContent.vue` ✅
- `composables/useSidebar.js` ✅

### **Flujo Funcional:**
```
/dashboard → DashboardController → Business/Index → BusinessLayout → DashboardContent
/products → ProductController → Business/Index → BusinessLayout → ProductsContent
/orders → OrdersController → Business/Index → BusinessLayout → OrdersContent
```

### **Archivos Legacy Eliminados:**
- `modules/dashboard/pages/Business.vue` ❌ (eliminado - reemplazado por SPA)
- `modules/dashboard/pages/Dashboard.vue` ❌ (eliminado - no se usaba)
- `modules/dashboard/pages/DashboardAdmin.vue` ❌ (eliminado - no se usaba)
- `modules/dashboard/pages/DashboardCustomer.vue` ✅ (mantenido - se usa para customers)

## ✅ **Ventajas de esta Arquitectura**

1. **✅ Consistencia:** Todos los SPAs siguen el mismo patrón
2. **✅ Escalabilidad:** Fácil agregar nuevos módulos
3. **✅ Mantenibilidad:** Código organizado y predecible
4. **✅ Testing:** Cada componente se puede probar independientemente
5. **✅ Reutilización:** Composables y layouts se reutilizan
6. **✅ Performance:** Solo carga el contenido necesario

## 🚀 **Comandos Rápidos**

```bash
# Crear nuevo SPA completo
mkdir -p resources/js/modules/[moduleName]/pages
mkdir -p resources/js/modules/[moduleName]/content
touch resources/js/layouts/[moduleName]Layout.vue
touch resources/js/modules/[moduleName]/pages/Index.vue
touch resources/js/modules/[moduleName]/content/[section]Content.vue

# Verificar estructura
tree resources/js/modules/[moduleName]
tree resources/js/layouts/
tree resources/js/composables/
```

## 📋 **Checklist de Implementación**

- [ ] Layout creado con estructura estándar
- [ ] Página Index creada (solo importa layout)
- [ ] Componentes de contenido creados
- [ ] Controller actualizado para renderizar Index
- [ ] app.js configurado para resolver rutas
- [ ] Composables creados si es necesario
- [ ] Testing de navegación entre secciones
- [ ] Testing de estado persistente (sidebar, etc.)

---

**Usa este workflow para todos los nuevos SPAs para mantener consistencia en todo el proyecto!** 🎯
