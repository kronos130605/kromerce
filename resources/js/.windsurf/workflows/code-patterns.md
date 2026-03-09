---
description: Code Patterns and Standards
---

# Code Patterns and Standards

## 🎯 Core Principles

### 1. Consistency First
- Always follow existing patterns in the codebase
- Maintain visual and functional consistency
- Use established color schemes and naming conventions

### 2. Performance Matters
- Optimize for runtime performance
- Minimize re-renders
- Use computed properties efficiently

### 3. Accessibility Always
- Semantic HTML structure
- ARIA labels and roles
- Keyboard navigation support
- Color contrast compliance

## 🏗️ Architecture Patterns

### Component Architecture
```javascript
// ✅ Preferred: Composition API with explicit imports
import { ref, computed, onMounted } from 'vue';
import { useDarkMode } from '@/composables/useDarkMode';

// ❌ Avoid: Mixing patterns
export default {
  data() { return {} },
  setup() { return {} }
}
```

### State Management
```javascript
// ✅ Local state with refs
const isLoading = ref(false);
const items = ref([]);

// ✅ Computed for derived state
const filteredItems = computed(() => 
  items.value.filter(item => item.active)
);
```

### Props Definition
```javascript
// ✅ Explicit prop definitions with types
const props = defineProps({
  title: {
    type: String,
    required: true
  },
  items: {
    type: Array,
    default: () => []
  }
});
```

## 🎨 Dark/Light Mode Patterns

### Decision Tree
```
Need complex logic or user interaction?
├── Yes → Use useDarkMode composable
│   ├── Toggle functionality
│   ├── LocalStorage persistence
│   └── System preference detection
└── No → Use Tailwind dark: classes
    ├── Simple UI elements
    ├── Hover states
    └── Static content
```

### Implementation Examples

#### Composable Pattern (Complex)
```javascript
const { isDark, toggleDarkMode } = useDarkMode();
const isDarkMode = computed(() => isDark.value);

const buttonClasses = computed(() => 
  isDarkMode.value 
    ? 'bg-gray-800 text-white hover:bg-gray-700'
    : 'bg-white text-gray-900 hover:bg-gray-100'
);
```

#### Tailwind Pattern (Simple)
```html
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white transition-colors">
  <p class="text-gray-600 dark:text-gray-300">
    Content here
  </p>
</div>
```

## 📁 File Organization

### Directory Structure
```
resources/js/
├── composables/           # Reusable logic
│   └── useDarkMode.js
├── components/            # Shared components
│   ├── ui/               # UI primitives
│   └── shared/           # Cross-module components
└── modules/             # Feature modules
    ├── auth/
    │   ├── components/
    │   └── pages/
    ├── dashboard/
    │   ├── components/
    │   │   └── tabs/
    │   └── pages/
    └── marketing/
        ├── components/
        └── pages/
```

### Naming Conventions
```
Files:           PascalCase.vue
Components:      PascalCase
Variables:        camelCase
Constants:        UPPER_SNAKE_CASE
Classes:          kebab-case
```

## 🔧 Code Style

### Vue 3 Composition API
```javascript
// ✅ Good: Explicit imports
import { ref, computed, onMounted } from 'vue';

// ✅ Good: Destructuring
const { isDark, toggleDarkMode } = useDarkMode();

// ✅ Good: Arrow functions
const handleClick = () => {
    toggleDarkMode();
};
```

### TypeScript (when applicable)
```typescript
// ✅ Good: Explicit interfaces
interface User {
    id: string;
    name: string;
    email?: string;
}

// ✅ Good: Generic types
interface ApiResponse<T> {
    data: T;
    status: string;
}
```

### CSS Classes
```html
<!-- ✅ Good: Responsive and dark mode aware -->
<div class="container mx-auto px-4 py-8 bg-white dark:bg-gray-800 rounded-lg">
  <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
    Title
  </h2>
</div>

<!-- ❌ Avoid: Magic numbers, inconsistent spacing -->
<div class="p-6 mt-4 text-xl">
  <div class="bg-gray-200">
    Content
  </div>
</div>
```

## 🎨 Color System Reference

### Primary Colors (Dark/Light)
```css
/* Light Mode */
--bg-primary: theme('colors.white');
--bg-secondary: theme('colors.gray.50');
--text-primary: theme('colors.gray.900');
--text-secondary: theme('colors.gray.600');

/* Dark Mode */
--bg-primary-dark: theme('colors.gray.800');
--bg-secondary-dark: theme('colors.gray.700');
--text-primary-dark: theme('colors.white');
--text-secondary-dark: theme('colors.gray.300');
```

### Interactive Colors
```css
/* Consistent across all components */
--blue-500: theme('colors.blue.500');
--blue-600: theme('colors.blue.600');
--green-500: theme('colors.green.500');
--red-500: theme('colors.red.500');
--yellow-500: theme('colors.yellow.500');

/* Dark mode variants */
--blue-400: theme('colors.blue.400');
--green-400: theme('colors.green.400');
--red-400: theme('colors.red.400');
--yellow-400: theme('colors.yellow.400');
```

## 🔄 Common Patterns

### Form Elements
```html
<!-- Input fields -->
<input 
  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 
         bg-white dark:bg-gray-700 text-gray-900 dark:text-white 
         rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
/>

<!-- Buttons -->
<button 
  class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white 
         rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 
         transition-colors duration-200"
>
  Button
</button>
```

### Cards and Containers
```html
<!-- Standard card -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg 
     border border-gray-200 dark:border-gray-700 p-6">
  <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
    Card Title
  </h3>
  <p class="text-gray-600 dark:text-gray-300">
    Card content
  </p>
</div>
```

### Navigation
```html
<!-- Tab navigation -->
<nav class="flex space-x-8 border-b border-gray-200 dark:border-gray-700">
  <button 
    class="py-2 px-1 border-b-2 font-medium text-sm 
           border-blue-500 text-blue-600 
           dark:border-blue-400 dark:text-blue-400"
  >
    Active Tab
  </button>
  <button 
    class="py-2 px-1 border-b-2 font-medium text-sm 
           border-transparent text-gray-500 hover:text-gray-700 
           dark:text-gray-400 dark:hover:text-gray-200 
           hover:border-gray-300 dark:hover:border-gray-600"
  >
    Inactive Tab
  </button>
</nav>
```

## ⚡ Performance Guidelines

### Computed Properties
```javascript
// ✅ Good: Efficient computed
const expensiveValue = computed(() => {
  return heavyCalculation(data.value);
});

// ✅ Good: Memoized derived state
const filteredItems = computed(() => {
  return items.value.filter(item => 
    item.status === 'active' && 
    item.category === selectedCategory.value
  );
});
```

### Event Handling
```javascript
// ✅ Good: Debounced search
const debouncedSearch = debounce((query) => {
  searchQuery.value = query;
}, 300);

// ✅ Good: Throttled scroll
const throttledScroll = throttle(() => {
  updateScrollPosition();
}, 100);
```

### Lazy Loading
```javascript
// ✅ Good: Async component loading
const AsyncComponent = defineAsyncComponent(() => 
  import('./HeavyComponent.vue')
);
```

## 🧪 Testing Patterns

### Component Testing
```javascript
// ✅ Test both modes
describe('Component', () => {
  it('renders correctly in light mode', () => {
    const wrapper = mount(Component);
    expect(wrapper.find('.bg-white').exists()).toBe(true);
  });

  it('renders correctly in dark mode', async () => {
    const wrapper = mount(Component);
    await wrapper.setData({ isDark: true });
    expect(wrapper.find('.bg-gray-800').exists()).toBe(true);
  });
});
```

## 📚 Documentation Standards

### Component Documentation
```javascript
/**
 * Component description
 * 
 * @example
 * <Component title="Example" items={items} />
 */
```

### JSDoc Comments
```javascript
/**
 * Toggle dark mode
 * @returns {boolean} New dark mode state
 */
const toggleDarkMode = () => {
  // Implementation
};
```

---

## 🎯 Golden Rules

1. **Always test both light and dark modes**
2. **Use existing patterns before creating new ones**
3. **Maintain consistency with the established color system**
4. **Write self-documenting code**
5. **Prioritize accessibility and performance**

When in doubt, look at similar components in the codebase and follow their patterns.
