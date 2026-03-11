---
description: Translation Management Workflow - Modular i18n System
---

# Translation Management Workflow

## Overview
This workflow describes how to manage translations in the modular i18n system for Kromerce.

## Architecture

### File Structure
```
resources/js/i18n/
├── i18n.js                    # Main i18n configuration
└── locales/
    ├── es/
    │   ├── common.json        # Common translations (nav, hero, footer, etc.)
    │   ├── auth.json          # Authentication related
    │   ├── dashboard.json     # Dashboard specific
    │   ├── products.json      # Product management
    │   ├── business.json      # Business dashboard
    │   └── errors.json        # Error messages
    └── en/
        ├── common.json        # English common translations
        ├── auth.json          # English authentication
        ├── dashboard.json     # English dashboard
        ├── products.json      # English product management
        ├── business.json      # English business dashboard
        └── errors.json        # English error messages
```

### Configuration
The main `i18n.js` file imports and merges all modular translation files:

```javascript
import { createI18n } from 'vue-i18n';

// Import all modules
import commonEs from './i18n/locales/es/common.json';
import authEs from './i18n/locales/es/auth.json';
// ... other imports

// Merge translations
const mergeTranslations = (common, auth, dashboard, products, business, errors) => ({
  ...common,
  ...auth,
  ...dashboard,
  ...products,
  ...business,
  ...errors
});

// Create i18n instance
const i18n = createI18n({
  legacy: false,
  locale: 'es',
  fallbackLocale: 'es',
  messages: {
    es: mergeTranslations(commonEs, authEs, dashboardEs, productsEs, businessEs, errorsEs),
    en: mergeTranslations(commonEn, authEn, dashboardEn, productsEn, businessEn, errorsEn)
  },
  globalInjection: true
});
```

## Adding New Translations

### 1. Identify the Module
Determine which module the translation belongs to:
- **common**: Navigation, hero sections, CTAs, pricing, features, testimonials, stats, PWA, footer
- **auth**: Login, registration, password reset
- **dashboard**: Dashboard-specific content, quick actions, events
- **products**: Product management, forms, validation, status
- **business**: Business dashboard, roles, profile settings
- **errors**: Error messages and notifications

### 2. Add Translation Keys
Add the translation to both Spanish and English files:

**Spanish (`es/[module].json`):**
```json
{
  "module_name": {
    "new_key": "Traducción en español",
    "another_key": "Otra traducción"
  }
}
```

**English (`en/[module].json`):**
```json
{
  "module_name": {
    "new_key": "English translation",
    "another_key": "Another translation"
  }
}
```

### 3. Use in Vue Components
```vue
<template>
  <div>{{ t('module_name.new_key') }}</div>
</template>

<script setup>
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
</script>
```

## Translation Key Naming Conventions

### Structure
```
module.section.subsection.key
```

### Examples
- `common.nav.home` - Navigation home link
- `auth.login.title` - Login page title
- `dashboard.orders.table.status` - Order status in table
- `products.form.name.placeholder` - Product name placeholder
- `business.roles.admin.title` - Admin role title
- `errors.access_denied` - Access denied error

### Guidelines
- Use lowercase and underscores for readability
- Group related keys under logical sections
- Keep key names descriptive but concise
- Maintain consistency across modules

## Finding Missing Translations

### 1. Console Warnings
Look for `[intlify] Not found 'key' in 'locale' messages` warnings in browser console.

### 2. Search Usage
```bash
# Find all translation usage in Vue files
grep -r "t('" resources/js/modules/
```

### 3. Verify Structure
Ensure both language files have identical structure:
```bash
# Check if keys exist in both files
jq -r 'paths | join(".")' resources/js/i18n/locales/es/products.json
jq -r 'paths | join(".")' resources/js/i18n/locales/en/products.json
```

## Maintenance Tasks

### Daily/Weekly
1. **Check Console**: Look for missing translation warnings
2. **Sync Files**: Ensure all keys exist in both languages
3. **Test Functionality**: Verify translations display correctly

### When Adding Features
1. **Plan Translations**: Identify all text that needs translation
2. **Add Keys**: Add to appropriate module files
3. **Update Components**: Use translation keys in new components
4. **Test**: Verify all translations work

### When Refactoring
1. **Update Keys**: Move keys to appropriate modules if needed
2. **Update Components**: Change component references
3. **Verify**: Ensure no broken references

## Troubleshooting

### Common Issues

#### 1. Translation Not Found
**Problem**: `[intlify] Not found 'key' in 'locale' messages`
**Solution**: 
- Check if key exists in both language files
- Verify JSON syntax is valid
- Restart development server

#### 2. JSON Syntax Error
**Problem**: Invalid JSON causing import failures
**Solution**:
- Use JSON validator
- Check for extra commas
- Verify bracket/brace matching

#### 3. Wrong Module Structure
**Problem**: Key in wrong module file
**Solution**:
- Move key to appropriate module
- Update component references
- Test functionality

### Debugging Steps

1. **Check Console**: Look for i18n warnings/errors
2. **Verify Imports**: Ensure `i18n.js` imports all modules
3. **Test Keys**: Use browser console to test specific keys:
   ```javascript
   // In browser console
   console.log(i18n.global.t('module.key'));
   ```
4. **Compare Files**: Ensure both language files match structure

## Best Practices

### 1. Consistency
- Use same key structure across languages
- Maintain consistent terminology
- Follow naming conventions

### 2. Organization
- Keep related keys together
- Use logical sections
- Avoid deeply nested structures

### 3. Performance
- Load only necessary modules (if needed)
- Use lazy loading for large translation sets
- Cache translations appropriately

### 4. Documentation
- Document module purposes
- Keep track of translation conventions
- Update documentation when adding modules

## Tools and Commands

### Development
```bash
# Restart development server to reload translations
npm run dev

# Check JSON syntax
node -e "console.log(JSON.parse(require('fs').readFileSync('file.json', 'utf8')))"
```

### Validation
```bash
# Find all translation usage
grep -r "t('" resources/js/modules/

# Compare file structures
diff <(jq -S . es.json) <(jq -S . en.json)
```

### Testing
- Test in both languages
- Verify all UI elements show translations
- Check responsive behavior with different text lengths

## Migration from Single File

If migrating from single `en.json`/`es.json` files:

1. **Backup**: Keep original files as backup
2. **Split**: Divide content into logical modules
3. **Update Config**: Modify `i18n.js` to use modular imports
4. **Test**: Verify all translations work
5. **Remove**: Delete old single files once confirmed working

## Emergency Procedures

### If Translations Stop Working
1. **Check Imports**: Verify `i18n.js` imports are correct
2. **Validate JSON**: Check for syntax errors
3. **Restart Server**: Clear cache and restart
4. **Rollback**: Use backup files if needed

### If Keys Missing After Update
1. **Sync Files**: Compare and sync both language files
2. **Check Structure**: Verify JSON structure is identical
3. **Clear Cache**: Restart development server
4. **Verify Components**: Check component key references
