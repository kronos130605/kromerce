---
description: Development Standards and Guidelines
---

# Development Standards and Guidelines

## Dark Mode Support Requirements

All new components and features must include comprehensive dark mode support using the established color system.

### Color System Standards

Use the following Tailwind CSS color classes for dark mode compatibility:

#### Background Colors
- **Primary backgrounds**: `bg-white dark:bg-gray-800`
- **Secondary backgrounds**: `bg-gray-50 dark:bg-gray-900`
- **Card backgrounds**: `bg-white dark:bg-gray-800`
- **Input backgrounds**: `bg-white dark:bg-gray-700`

#### Text Colors
- **Primary text**: `text-gray-900 dark:text-white`
- **Secondary text**: `text-gray-600 dark:text-gray-300`
- **Muted text**: `text-gray-500 dark:text-gray-400`
- **Border text**: `text-gray-400 dark:text-gray-500`

#### Border Colors
- **Primary borders**: `border-gray-200 dark:border-gray-700`
- **Input borders**: `border-gray-300 dark:border-gray-600`
- **Focus borders**: `border-blue-500 dark:border-blue-400`

#### Interactive Elements
- **Buttons**: `bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600`
- **Destructive actions**: `bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600`
- **Secondary actions**: `bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600`

### Implementation Checklist

For every new component, ensure:

- [ ] All background colors have dark variants
- [ ] All text colors have dark variants
- [ ] All border colors have dark variants
- [ ] Interactive states (hover, focus) have dark variants
- [ ] Component is tested in both light and dark modes

## Internationalization (i18n) Standards

All user-facing text must be internationalized using Vue I18n.

### File Structure

```
resources/js/i18n/locales/
├── en.json       # All English translations
└── es.json       # All Spanish translations
```

### Translation Keys Standards

Use dot notation for nested keys and follow naming conventions:

#### Naming Conventions
- **Use snake_case for keys**: `product_name`, `user_profile`
- **Group related items**: `products_management.list.title`, `products_management.form.name`
- **Use descriptive names**: `create_product` instead of `cp`
- **Include context**: `button.save`, `message.success`
- **Use feature prefixes**: `products_management.*`, `dashboard.*`, `auth.*`

#### Example Structure

```json
{
  "products_management": {
    "title": "Products",
    "list": {
      "title": "Product List",
      "empty": "No products found",
      "search_placeholder": "Search products..."
    },
    "form": {
      "name": "Product Name",
      "description": "Description",
      "price": "Price",
      "save": "Save Product",
      "cancel": "Cancel"
    },
    "status": {
      "active": "Active",
      "inactive": "Inactive",
      "draft": "Draft"
    },
    "messages": {
      "created": "Product created successfully",
      "updated": "Product updated successfully",
      "deleted": "Product deleted successfully",
      "error": "An error occurred"
    }
  }
}
```

### Vue Component Implementation

#### Using useI18n Composable

```vue
<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

// Use translation keys
const title = t('products.title');
const saveText = t('products.form.save');
</script>

<template>
  <h1>{{ t('products.list.title') }}</h1>
  <button>{{ t('products.form.save') }}</button>
</template>
```

#### Dynamic Translations with Parameters

```json
{
  "products": {
    "count": "{count} products found",
    "deleted": "Product '{name}' deleted successfully"
  }
}
```

```vue
<script setup>
const { t } = useI18n();

const productCount = t('products.count', { count: products.length });
const deleteMessage = t('products.deleted', { name: product.name });
</script>
```

### Required Translation Files

All new features must include translations in both English (`en`) and Spanish (`es`).

#### Minimum Required Files
- `en.json` - All English translations
- `es.json` - All Spanish translations

#### Common Translations Template

```json
{
  "buttons": {
    "save": "Save",
    "cancel": "Cancel",
    "delete": "Delete",
    "edit": "Edit",
    "create": "Create",
    "update": "Update",
    "search": "Search",
    "filter": "Filter",
    "clear": "Clear",
    "back": "Back",
    "next": "Next",
    "previous": "Previous"
  },
  "messages": {
    "loading": "Loading...",
    "success": "Success",
    "error": "Error",
    "warning": "Warning",
    "info": "Info",
    "no_data": "No data available",
    "confirm_delete": "Are you sure you want to delete this item?"
  },
  "navigation": {
    "home": "Home",
    "dashboard": "Dashboard",
    "settings": "Settings",
    "profile": "Profile",
    "logout": "Logout"
  },
  "status": {
    "active": "Active",
    "inactive": "Inactive",
    "pending": "Pending",
    "completed": "Completed",
    "cancelled": "Cancelled"
  }
}
```

## Component Development Standards

### File Naming
- **Vue components**: PascalCase (e.g., `ProductList.vue`, `UserForm.vue`)
- **Translation files**: snake_case (e.g., `product_management.json`)

### Props and Emits
- **Props**: camelCase with clear names
- **Emits**: kebab-case with descriptive names

### Accessibility Standards
- All interactive elements must have proper ARIA labels
- Use semantic HTML elements
- Ensure keyboard navigation support
- Include focus indicators in both themes

## Testing Requirements

### Visual Testing
- Test all components in both light and dark modes
- Verify color contrast ratios meet WCAG standards
- Test responsive design at different breakpoints

### Functional Testing
- Test all interactive states (hover, focus, active, disabled)
- Verify form validation messages are translated
- Test error states and loading states

## Code Review Checklist

Before submitting any PR, verify:

### Dark Mode
- [ ] All colors have dark variants
- [ ] Component looks good in both themes
- [ ] No hardcoded colors
- [ ] Proper contrast ratios

### Internationalization
- [ ] All user-facing text uses `t()` function
- [ ] Translations exist in both languages
- [ ] No hardcoded strings in templates
- [ ] Dynamic content properly handles parameters

### General
- [ ] Component follows naming conventions
- [ ] Proper TypeScript types (if applicable)
- [ ] Accessibility requirements met
- [ ] Responsive design implemented
