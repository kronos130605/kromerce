---
description: Development Guidelines and Standards
---

# Development Guidelines and Standards

## 🖥️ WSL/NVM Development Protocol

### **Command Execution Workflow**
1. **Assistant provides command** with explanation
2. **Developer executes** in WSL/NVM environment
3. **Developer confirms** result (success/failure)
4. **Assistant continues** or provides alternative
5. **Working commands** documented for future use

### **WSL Command Standards**
```bash
# Laravel Artisan (WSL)
php artisan make:migration migration_name
php artisan migrate
php artisan tinker

# Package Management (WSL)
composer install --optimize-autoloader
npm install
npm run build

# Database (WSL)
mysql -u root -p database_name
```

### **NVM Version Handling**
```bash
# Check current PHP version
php --version

# Use specific PHP version if needed
php8.2 artisan command
php8.1 artisan command

# Check Node version
node --version
npm --version
```

### **Command Testing Rules**
- Always test in your environment first
- Report exact error messages
- Document working commands in workflow
- Use documented commands for consistency

---

## 🎯 Core Development Philosophy

### 1. Consistency Over Convenience
Always follow established patterns even if they seem more verbose. Consistency creates maintainable code.

### 2. User Experience First
Every decision should prioritize the end-user experience, including accessibility and performance.

### 3. Test-Driven Development
Write tests alongside code, not as an afterthought.

## 📁 Project Structure Standards

### Module Organization
```
resources/js/
├── composables/           # Reusable Vue logic
│   └── useDarkMode.js    # Centralized dark mode logic
├── components/            # Shared across modules
│   ├── ui/               # UI primitives (Button, Input, etc.)
│   └── shared/           # Cross-module components
├── modules/             # Feature-based organization
│   ├── auth/            # Authentication flows
│   │   ├── components/ # Auth-specific components
│   │   └── pages/       # Auth pages (Login, Register)
│   ├── dashboard/       # Main dashboard
│   │   ├── components/ # Dashboard components
│   │   └── pages/       # Dashboard pages
│   └── marketing/       # Marketing/landing pages
│       ├── components/ # Marketing components
│       └── pages/       # Marketing pages
└── .windsurf/workflows/ # Development workflows
```

### File Naming Conventions
```
Components:     PascalCase.vue      (UserProfile.vue)
Pages:           PascalCase.vue      (Dashboard.vue)
Composables:     camelCase.js        (useUserData.js)
Utilities:       camelCase.js        (formatDate.js)
Types:           camelCase.ts        (userTypes.ts)
```

## 🏗️ Vue 3 Standards

### Composition API Only
```javascript
// ✅ CORRECT: Composition API
<script setup>
import { ref, computed, onMounted } from 'vue';
import { useDarkMode } from '@/composables/useDarkMode';

const { isDark } = useDarkMode();
const isDarkMode = computed(() => isDark.value);

onMounted(() => {
    // Component logic
});
</script>

// ❌ AVOID: Options API
<script>
export default {
  data() { return {} },
  methods: {}
}
</script>
```

### Props Definition
```javascript
// ✅ CORRECT: Explicit prop definitions
const props = defineProps({
  title: {
    type: String,
    required: true
  },
  items: {
    type: Array,
    default: () => []
  },
  isLoading: {
    type: Boolean,
    default: false
  }
});

// ❌ AVOID: Vague prop definitions
const props = defineProps(['title', 'items', 'isLoading']);
```

### Emits Definition
```javascript
// ✅ CORRECT: Explicit emits
const emit = defineEmits(['update:modelValue', 'item-clicked', 'loading-changed']);

// Usage
emit('item-clicked', { id: 1, name: 'Item 1' });
```

### Reactive Data
```javascript
// ✅ CORRECT: Use refs for primitive values
const count = ref(0);
const user = ref({ name: '', email: '' });

// ✅ CORRECT: Use computed for derived state
const doubleCount = computed(() => count.value * 2);
const fullName = computed(() => `${user.value.name} ${user.value.lastName}`);

// ✅ CORRECT: Use watchers for side effects
watch(count, (newValue, oldValue) => {
  console.log(`Count changed from ${oldValue} to ${newValue}`);
});
```

## 🎨 Styling Standards

### Tailwind CSS First
Always use Tailwind CSS classes before custom CSS. Only use custom CSS for complex animations or specific requirements.

### Dark Mode Implementation
```javascript
// Decision tree for dark mode implementation
const needsComplexLogic = componentHasUserInteraction || componentHasComplexState;

if (needsComplexLogic) {
  // Use composable for complex components
  const { isDark, toggleDarkMode } = useDarkMode();
  const isDarkMode = computed(() => isDark.value);
} else {
  // Use Tailwind dark: classes for simple components
  // No JavaScript needed
}
```

### Color System
```html
<!-- Always use semantic color names -->
<div class="bg-white dark:bg-gray-800">
  <h2 class="text-gray-900 dark:text-white">Title</h2>
  <p class="text-gray-600 dark:text-gray-300">Description</p>
</div>

<!-- Never use arbitrary values unless absolutely necessary -->
<div class="bg-[#f3f4f6]"> <!-- AVOID THIS -->
```

### Responsive Design
```html
<!-- Mobile-first approach -->
<div class="p-4 md:p-6 lg:p-8">
  <h1 class="text-lg md:text-xl lg:text-2xl">Responsive Title</h1>
</div>
```

## 🔧 Code Quality Standards

### TypeScript (when applicable)
```typescript
// ✅ CORRECT: Explicit interfaces
interface User {
  id: string;
  name: string;
  email?: string;
  createdAt: Date;
}

interface ApiResponse<T> {
  data: T;
  status: 'success' | 'error';
  message?: string;
}

// ✅ CORRECT: Generic functions
function fetchData<T>(url: string): Promise<ApiResponse<T>> {
  return fetch(url).then(res => res.json());
}
```

### Error Handling
```javascript
// ✅ CORRECT: Try-catch with proper error handling
try {
  const result = await apiCall();
  return result;
} catch (error) {
  console.error('API call failed:', error);
  throw new Error('Failed to fetch data');
}

// ✅ CORRECT: Error boundaries
const error = ref(null);
const isLoading = ref(false);

const loadData = async () => {
  try {
    isLoading.value = true;
    error.value = null;
    data.value = await fetchData();
  } catch (err) {
    error.value = err.message;
  } finally {
    isLoading.value = false;
  }
};
```

### Performance Guidelines
```javascript
// ✅ CORRECT: Efficient computed properties
const expensiveValue = computed(() => {
  // Only recomputes when dependencies change
  return heavyCalculation(data.value);
});

// ✅ CORRECT: Debounced search
import { debounce } from 'lodash-es';

const debouncedSearch = debounce((query) => {
  searchQuery.value = query;
}, 300);

// ✅ CORRECT: Lazy loading
const AsyncComponent = defineAsyncComponent(() => 
  import('./HeavyComponent.vue')
);
```

## 🧪 Testing Standards

### Unit Testing
```javascript
// ✅ CORRECT: Test component behavior
describe('UserProfile', () => {
  it('should render user information correctly', () => {
    const user = { name: 'John Doe', email: 'john@example.com' };
    const wrapper = mount(UserProfile, { props: { user } });
    
    expect(wrapper.find('[data-testid="user-name"]').text()).toBe('John Doe');
    expect(wrapper.find('[data-testid="user-email"]').text()).toBe('john@example.com');
  });

  it('should emit event when edit button is clicked', async () => {
    const wrapper = mount(UserProfile, { props: { user: mockUser } });
    
    await wrapper.find('[data-testid="edit-button"]').trigger('click');
    
    expect(wrapper.emitted('edit')).toBeTruthy();
    expect(wrapper.emitted('edit')[0]).toEqual([mockUser]);
  });
});
```

### Integration Testing
```javascript
// ✅ CORRECT: Test user flows
describe('User Registration Flow', () => {
  it('should complete registration successfully', async () => {
    const wrapper = mount(RegisterPage);
    
    await wrapper.find('[data-testid="name-input"]').setValue('John Doe');
    await wrapper.find('[data-testid="email-input"]').setValue('john@example.com');
    await wrapper.find('[data-testid="password-input"]').setValue('password123');
    await wrapper.find('[data-testid="submit-button"]').trigger('click');
    
    // Assert navigation to dashboard
    expect(router.currentRoute.value.name).toBe('dashboard');
  });
});
```

### Dark Mode Testing
```javascript
// ✅ CORRECT: Test both themes
describe('Dark Mode Support', () => {
  it('should render correctly in light mode', () => {
    const wrapper = mount(Component);
    expect(wrapper.find('.bg-white').exists()).toBe(true);
    expect(wrapper.find('.text-gray-900').exists()).toBe(true);
  });

  it('should render correctly in dark mode', async () => {
    const wrapper = mount(Component);
    await wrapper.setData({ isDarkMode: true });
    
    expect(wrapper.find('.bg-gray-800').exists()).toBe(true);
    expect(wrapper.find('.text-white').exists()).toBe(true);
  });
});
```

## 📝 Documentation Standards

### Component Documentation
```javascript
/**
 * User profile card component
 * 
 * @component
 * @example
 * <UserProfile 
 *   :user="userData" 
 *   :show-edit-button="true"
 *   @edit="handleEdit"
 * />
 */
```

### JSDoc Comments
```javascript
/**
 * Fetches user data from API
 * @param {string} userId - The user ID to fetch
 * @returns {Promise<User>} User data object
 * @throws {Error} When user not found
 */
const fetchUser = async (userId) => {
  // Implementation
};
```

### README Files
Each major module should have a README.md explaining:
- Purpose and functionality
- Component breakdown
- Usage examples
- Testing instructions

## 🔄 Code Review Process

### Pre-commit Checklist
- [ ] Code follows established patterns
- [ ] Dark mode implemented correctly
- [ ] Tests written and passing
- [ ] Documentation updated
- [ ] No console errors or warnings
- [ ] Accessibility standards met
- [ ] Performance implications considered

### Review Guidelines
1. **Functionality**: Does it work as expected?
2. **Code Quality**: Is it clean and maintainable?
3. **Dark Mode**: Are both themes properly implemented?
4. **Accessibility**: Is it accessible to all users?
5. **Performance**: Will it impact app performance?
6. **Testing**: Are tests comprehensive?

## 🚀 Deployment Standards

### Build Process
- All components must build without errors
- No console warnings in production
- Assets properly optimized
- Environment variables properly configured

### Release Checklist
- [ ] All tests passing
- [ ] Manual testing completed
- [ ] Documentation updated
- [ ] Breaking changes documented
- [ ] Performance benchmarks met

## 📚 Learning Resources

### Internal References
- Component creation workflow: `/workflows/component-creation.md`
- Code patterns: `/workflows/code-patterns.md`
- Dark mode standards: `/workflows/dark-mode-standards.md`

### External References
- [Vue 3 Documentation](https://vuejs.org/)
- [Tailwind CSS Documentation](https://tailwindcss.com/)
- [Web Accessibility Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)

---

## 🎯 Golden Rules

1. **Always implement dark mode** - No exceptions
2. **Follow existing patterns** - Don't reinvent
3. **Write tests** - Quality is non-negotiable
4. **Think accessibility** - Include everyone
5. **Optimize for performance** - Users notice speed
6. **Document your code** - Future you will thank you

When in doubt, ask for clarification rather than guessing. Consistency is key to maintainable code.
