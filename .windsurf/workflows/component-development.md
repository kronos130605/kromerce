---
description: Component Development Workflow
---

# Component Development Workflow

## Before You Start

1. **Read the Development Standards** - Review `/development-standards.md`
2. **Check Existing Components** - Look for similar components before creating new ones
3. **Plan Your Approach** - Consider dark mode and i18n requirements upfront

## Step-by-Step Component Creation

### 1. Setup Component Structure

```bash
# Create component file with proper naming
touch resources/js/modules/[feature]/components/[ComponentName].vue
```

### 2. Implement Dark Mode Support

Use the established color system:

```vue
<template>
  <!-- Backgrounds -->
  <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
    <!-- Text -->
    <h2 class="text-gray-900 dark:text-white font-semibold">
      {{ title }}
    </h2>
    <p class="text-gray-600 dark:text-gray-300 text-sm">
      {{ description }}
    </p>
    
    <!-- Borders -->
    <div class="border border-gray-200 dark:border-gray-700 rounded">
      <!-- Content -->
    </div>
    
    <!-- Interactive Elements -->
    <button class="bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white px-4 py-2 rounded">
      {{ buttonText }}
    </button>
  </div>
</template>
```

### 3. Add Internationalization

#### Step 3a: Add Translation Keys

Add to appropriate JSON file (e.g., `resources/js/locales/en/products.json`):

```json
{
  "component_name": {
    "title": "Component Title",
    "description": "Component description text",
    "button_text": "Save Changes",
    "error_message": "An error occurred"
  }
}
```

#### Step 3b: Add Spanish Translations

Add to `resources/js/locales/es/products.json`:

```json
{
  "component_name": {
    "title": "Título del Componente",
    "description": "Texto de descripción del componente",
    "button_text": "Guardar Cambios",
    "error_message": "Ocurrió un error"
  }
}
```

#### Step 3c: Use in Component

```vue
<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

// Component logic here
</script>

<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
    <h2 class="text-gray-900 dark:text-white font-semibold">
      {{ t('component_name.title') }}
    </h2>
    <p class="text-gray-600 dark:text-gray-300 text-sm">
      {{ t('component_name.description') }}
    </p>
    
    <button class="bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white px-4 py-2 rounded">
      {{ t('component_name.button_text') }}
    </button>
  </div>
</template>
```

### 4. Add Component Props and Emits

```vue
<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

// Define props with proper typing
const props = defineProps({
  title: {
    type: String,
    default: () => t('component_name.title')
  },
  description: {
    type: String,
    default: () => t('component_name.description')
  },
  disabled: {
    type: Boolean,
    default: false
  }
});

// Define emits
const emit = defineEmits(['save', 'cancel', 'update']);

// Component methods
const handleSave = () => {
  emit('save');
};

const handleCancel = () => {
  emit('cancel');
};
</script>
```

### 5. Add Accessibility Features

```vue
<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
    <!-- Semantic HTML with ARIA labels -->
    <section aria-labelledby="component-title">
      <h2 id="component-title" class="text-gray-900 dark:text-white font-semibold">
        {{ title }}
      </h2>
      
      <p class="text-gray-600 dark:text-gray-300 text-sm">
        {{ description }}
      </p>
    </section>
    
    <!-- Accessible button with states -->
    <button
      @click="handleSave"
      :disabled="disabled"
      :aria-label="t('component_name.button_text')"
      class="bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 disabled:bg-gray-400 dark:disabled:bg-gray-600 text-white px-4 py-2 rounded focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
    >
      {{ t('component_name.button_text') }}
    </button>
  </div>
</template>
```

### 6. Add Responsive Design

```vue
<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 lg:p-8">
    <!-- Responsive grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <!-- Content -->
    </div>
    
    <!-- Responsive text -->
    <h2 class="text-lg sm:text-xl lg:text-2xl text-gray-900 dark:text-white font-semibold">
      {{ title }}
    </h2>
  </div>
</template>
```

## Quality Assurance Checklist

### Before Committing

#### Dark Mode Testing
- [ ] Component renders correctly in light mode
- [ ] Component renders correctly in dark mode
- [ ] All interactive states have proper dark variants
- [ ] No color contrast issues
- [ ] No hardcoded colors

#### Internationalization Testing
- [ ] All text uses translation keys
- [ ] English translations display correctly
- [ ] Spanish translations display correctly
- [ ] No hardcoded strings in template
- [ ] Dynamic parameters work correctly

#### Accessibility Testing
- [ ] Semantic HTML elements used
- [ ] Proper ARIA labels and roles
- [ ] Keyboard navigation works
- [ ] Focus indicators visible
- [ ] Screen reader friendly

#### Responsive Testing
- [ ] Mobile layout (320px+)
- [ ] Tablet layout (768px+)
- [ ] Desktop layout (1024px+)
- [ ] Large desktop layout (1440px+)

#### Functionality Testing
- [ ] All props work correctly
- [ ] All emits fire correctly
- [ ] Error states handled
- [ ] Loading states handled
- [ ] Form validation works

## Common Patterns

### Loading State

```vue
<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
    <div v-if="loading" class="flex items-center justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 dark:border-blue-400"></div>
      <span class="ml-2 text-gray-600 dark:text-gray-300">
        {{ t('common.loading') }}
      </span>
    </div>
    
    <div v-else>
      <!-- Component content -->
    </div>
  </div>
</template>
```

### Error State

```vue
<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
    <div v-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
      <div class="flex items-center">
        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <span class="ml-2 text-red-800 dark:text-red-200">
          {{ error }}
        </span>
      </div>
    </div>
    
    <div v-else>
      <!-- Component content -->
    </div>
  </div>
</template>
```

### Empty State

```vue
<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
    <div v-if="items.length === 0" class="text-center py-8">
      <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
        {{ t('component_name.empty_title') }}
      </h3>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        {{ t('component_name.empty_description') }}
      </p>
    </div>
    
    <div v-else>
      <!-- Component content -->
    </div>
  </div>
</template>
```

## Integration Guidelines

### Adding to Existing Features

1. **Check existing patterns** - Follow the established conventions
2. **Update translations** - Add new keys to both language files
3. **Test integration** - Ensure component works with existing features
4. **Update documentation** - Document any new props or emits

### Creating New Feature Modules

1. **Create directory structure**:
   ```
   resources/js/modules/[feature]/
   ├── components/
   ├── pages/
   ├── composables/
   └── types/
   ```

2. **Create translation files**:
   ```
   resources/js/locales/
   ├── en/[feature].json
   └── es/[feature].json
   ```

3. **Follow naming conventions** - Be consistent with existing code

4. **Add comprehensive tests** - Cover all states and interactions

## Troubleshooting

### Common Dark Mode Issues

**Problem**: Colors not changing in dark mode
**Solution**: Ensure all color classes have `dark:` variants

**Problem**: Poor contrast in dark mode
**Solution**: Use the established color system, don't create custom colors

### Common i18n Issues

**Problem**: Translation not showing
**Solution**: Check that the key exists in both language files

**Problem**: Pluralization not working
**Solution**: Use Vue I18n's pluralization features

### Common Accessibility Issues

**Problem**: Focus not visible
**Solution**: Add `focus:` classes with proper ring/outline styles

**Problem**: Screen reader not announcing content
**Solution**: Add proper ARIA labels and semantic HTML
