---
description: TODOs Tracker - Pending Tasks Registry
---

# TODOs Tracker

Registro centralizado de tareas pendientes, ideas y mejoras planificadas.

> **Última actualización:** 2026-03-25  
> **Próxima revisión:** Cuando se complete Fase 4

---

## 🎯 Alta Prioridad

### Frontend - Productos (Fase 4)
| ID | Tarea | Ubicación | Notas | Estado |
|----|-------|-----------|-------|--------|
| TODO-4d | Crear `useProductManager` composable | `resources/js/modules/products/composables/` | Reutilizable para CRUD de productos | ⏳ Pendiente |
| TODO-4e | Crear `ProductModal.vue` (Opción A) | `resources/js/modules/products/components/` | Modal con tabs para wizard de 4 pasos | ⏳ Pendiente |
| TODO-4f | Crear `ProductView.vue` slider | `resources/js/modules/products/components/` | Vista lateral tipo drawer | ⏳ Pendiente |
| TODO-4g | Consolidar `Index.vue` con modals | `resources/js/modules/products/pages/Index.vue` | Integrar tabla + modals + slider | ⏳ Pendiente |
| TODO-4h | Eliminar páginas separadas Create/Edit/Show | `resources/js/modules/products/pages/` | Mantener solo Index.vue | ⏳ Pendiente |
| TODO-4i | Actualizar rutas de productos | `routes/web.php` o ruta Inertia | Apuntar todo a Index con query params | ⏳ Pendiente |

---

## 🔧 Media Prioridad

### Configuración de UI
| ID | Tarea | Ubicación | Notas | Estado |
|----|-------|-----------|-------|--------|
| TODO-CONFIG-001 | Agregar setting "modal_type" en store_settings | `store_settings` tabla / config | Opciones: 'modal' (A) o 'fullscreen' (B) | ⏳ Pendiente |
| TODO-CONFIG-002 | Crear UI para seleccionar tipo de modal | `modules/profile/pages/Settings.vue` | Toggle o select para tipo de modal | ⏳ Pendiente |
| TODO-CONFIG-003 | Implementar lógica dinámica de modal type | `composables/useModalType.js` | Lee setting y renderiza A o B | ⏳ Pendiente |

### Backend - Dark Mode Estandarizado
| ID | Tarea | Ubicación | Notas | Estado |
|----|-------|-----------|-------|--------|
| TODO-DARK-001 | Estandarizar clases dark en componentes legacy | `resources/js/components/dashboard/` | Reemplazar `dark:bg-gray-800` inconsistentes | ⏳ Pendiente |

---

## 💡 Ideas Futuras (Baja Prioridad)

| ID | Idea | Contexto | Beneficio |
|----|------|----------|-----------|
| IDEA-001 | Implementar Opción B (full-screen overlay) | Productos, luego otros módulos | Mejor UX para wizards complejos |
| IDEA-002 | Extraer `useEntityManager` composable genérico | Todos los módulos CRUD | Reutilizar patrón en orders, customers, etc. |
| IDEA-003 | Crear componente `EntityModal` genérico | `components/shared/` | Base reutilizable para todos los modals |
| IDEA-004 | Animaciones de transición entre modals | CSS/Tailwind | UX más pulida |
| IDEA-005 | Atajos de teclado para modals | `composables/useKeyboardShortcuts.js` | ESC para cerrar, Ctrl+S para guardar | 

---

## ✅ Completados Recientemente

| ID | Tarea | Fecha | Notas |
|----|-------|-------|-------|
| DONE-4a | Limpiar console.logs debug | 2026-03-25 | 4 archivos limpiados |
| DONE-4b | Crear workflow SPA Admin Pattern | 2026-03-25 | `spa-admin-modals-pattern.md` |
| DONE-4c | Análisis de páginas a consolidar | 2026-03-25 | 4 páginas identificadas |

---

## 📝 Cómo usar este tracker

### Agregar un nuevo TODO:
1. Buscar la sección de prioridad correcta
2. Asignar ID único: `TODO-{area}-{num}`
3. Completar todas las columnas
4. Estado inicial: ⏳ Pendiente

### Actualizar estado:
- ⏳ Pendiente → 🔄 En progreso → ✅ Completado

### Mover a Completados:
1. Copiar fila a sección "Completados Recientemente"
2. Agregar fecha y notas
3. Eliminar de sección original

---

## 🔗 Archivos relacionados

- `spa-admin-modals-pattern.md` — Estándar de implementación
- `component-development.md` — Workflow de componentes Vue
- `development-standards.md` — Estándares frontend
