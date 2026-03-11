# Kromerce Icon System

## Overview

Professional SVG icon system for consistent design across the Kromerce application. All icons are centralized, optimized, and easily accessible through the `Icon` component.

## Structure

```
resources/js/icons/
├── index.js          # Main icon library and exports
├── README.md         # This documentation
└── categories/
    ├── customer/      # Customer-specific icons
    ├── business/      # Business-specific icons
    └── ui/           # General UI icons
```

## Usage

### Basic Usage

```vue
<template>
  <!-- Basic icon -->
  <Icon name="dashboard" />
  
  <!-- With category -->
  <Icon name="products" category="business" />
  
  <!-- With custom size -->
  <Icon name="search" size="w-6 h-6" />
  
  <!-- With custom class -->
  <Icon name="bell" custom-class="text-red-500" />
</template>

<script setup>
import Icon from '@/components/ui/Icon.vue';
</script>
```

### Available Categories

- **`customer`** - Icons for customer-facing features
- **`business`** - Icons for business/admin features  
- **`ui`** - General UI icons (close, menu, etc.)
- **`all`** - Search in all categories (default)

### Icon Categories

#### Customer Icons
- `dashboard` - Home/overview icon
- `stores` - Store/shop icon
- `orders` - Shopping cart/orders icon
- `wishlist` - Heart/favorites icon
- `profile` - User profile icon
- `settings` - Settings/gear icon

#### Business Icons
- `dashboard` - Analytics/grid icon
- `products` - Package/product icon
- `orders` - Clipboard/orders icon
- `inventory` - Box/inventory icon
- `customers` - User group icon
- `analytics` - Chart/analytics icon
- `marketing` - Megaphone/marketing icon
- `reports` - Document/reports icon
- `settings` - Settings/gear icon

#### UI Icons
- `close` - X/close icon
- `menu` - Hamburger menu icon
- `chevronLeft` - Left arrow
- `chevronRight` - Right arrow
- `search` - Search magnifying glass
- `bell` - Notification bell
- `user` - User silhouette
- `logout` - Sign out icon

## Adding New Icons

### 1. Add to Icon Library

Edit `resources/js/icons/index.js` and add your icon to the appropriate category:

```javascript
export const BusinessIcons = {
  // ...existing icons
  newFeature: {
    name: 'newFeature',
    svg: 'M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5',
    viewBox: '0 0 24 24',
    size: 'w-5 h-5'
  }
};
```

### 2. Icon Properties

Each icon object should have:
- `name` - Unique identifier
- `svg` - SVG path data (without the `<path>` tag)
- `viewBox` - SVG viewBox (usually '0 0 24 24')
- `size` - Default size classes (usually 'w-5 h-5')

### 3. Use in Components

```vue
<Icon name="newFeature" category="business" />
```

## Navigation Integration

Icons are automatically integrated with the navigation system through `useNavigation()` composable:

```javascript
// The navigation items automatically use the new icon system
const sidebarNavigationItems = computed(() => [
  { 
    name: 'dashboard', 
    label: 'Dashboard', 
    icon: BusinessIcons.dashboard, // Full icon object
    href: '/dashboard',
    active: currentUrl.startsWith('/dashboard')
  }
]);
```

## Design Guidelines

### Consistency
- All icons use `stroke="currentColor"` for theme compatibility
- Consistent `stroke-width="2"` for visual harmony
- Standard `viewBox="0 0 24 24"` for uniform sizing

### Sizing
- Default size: `w-5 h-5` (20px)
- Small: `w-4 h-4` (16px)
- Large: `w-6 h-6` (24px)
- XL: `w-8 h-8` (32px)

### States
- Hover: Apply `hover:text-gray-900` or similar
- Active: Apply `text-blue-600` or theme colors
- Disabled: Apply `text-gray-400 opacity-50`

## Best Practices

1. **Use semantic names** - `userProfile` instead of `icon1`
2. **Keep it simple** - Avoid overly complex paths
3. **Maintain consistency** - Follow existing stroke and sizing patterns
4. **Test in themes** - Ensure icons work in both light and dark modes
5. **Accessibility** - Icons should be decorative, add text labels for functionality

## Performance

- Icons are inline SVGs (no HTTP requests)
- Optimized paths for minimal file size
- Cached by Vue's reactivity system
- No external dependencies

## Troubleshooting

### Icon not showing
1. Check the icon name spelling
2. Verify the category is correct
3. Ensure the icon exists in the library

### Icon looks wrong size
1. Check the default size in the icon definition
2. Use the `size` prop to override
3. Verify CSS classes aren't conflicting

### Icon not themed correctly
1. Ensure `stroke="currentColor"` is set
2. Check for overriding CSS styles
3. Verify dark mode CSS is working
