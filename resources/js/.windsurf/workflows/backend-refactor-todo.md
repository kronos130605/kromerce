---
description: Backend Refactor TODO List
---

# Backend Refactor TODO List

## 🎯 Objective
Systematically refactor Kromerce backend to follow clean architecture patterns with proper multitenant support and dark mode integration.

---

## 🖥️ WSL/NVM Command Standards

### **Command Execution Protocol**
1. **I provide commands** → You execute → You confirm → I continue
2. **WSL Compatibility** → All commands tested for WSL environment
3. **NVM Considerations** → PHP version compatibility handled
4. **Workflow Updates** → Working commands documented for future reference

### **Standard Command Patterns**
```bash
# Laravel Commands (WSL)
php artisan make:migration create_table_name
php artisan migrate
php artisan tinker

# Node/Composer (WSL)
composer install
npm install
npm run dev

# Database (WSL)
mysql -u root -p
```

### **Command Testing Protocol**
1. **I suggest command** → You test in your environment
2. **If fails** → I provide alternative → You test
3. **When works** → I update workflow with correct command
4. **Future reference** → Use documented working commands

---

## 🔄 Development Workflow

### **Step-by-Step Process**
1. **I provide command** with explanation
2. **You execute** in your WSL/NVM environment
3. **You confirm** result (success/failure)
4. **I continue** or provide alternative
5. **Working commands** get documented in workflow

### **Example Interaction**
```
🤖: Run: php artisan make:migration add_dark_mode_to_users
👤: [Executes] Command failed: PHP version not found
🤖: Try: php8.2 artisan make:migration add_dark_mode_to_users
👤: [Executes] Success!
🤖: [Updates workflow with correct PHP command]
```

---

## 📋 Priority Levels
- 🔴 **CRITICAL**: Security/Stability issues
- 🟡 **HIGH**: Core functionality improvements  
- 🟢 **MEDIUM**: Code quality/maintainability
- 🔵 **LOW**: Nice to have optimizations

---

## 🔴 CRITICAL - Phase 1: Foundation

### **1.1 Fix BaseRepository Security Issues**
- [ ] **Fix SQL Injection vulnerability** in `getBy()` method
- [ ] **Add input validation** for all criteria parameters
- [ ] **Implement tenant scope** properly in all methods
- [ ] **Add proper error handling** for database operations

**Current Problem:**
```php
// ❌ VULNERABLE - No validation
foreach ($criteria as $key => $value) {
    $query->where($key, $value); // SQL injection risk
}
```

**Target Solution:**
```php
// ✅ SECURE - Validated and scoped
protected function applyCriteria(Builder $query, array $criteria): Builder
{
    $allowedFields = $this->getAllowedFields();
    
    foreach ($criteria as $key => $value) {
        if (!in_array($key, $allowedFields)) {
            continue; // Skip invalid fields
        }
        
        if (is_array($value)) {
            $query->whereIn($key, $value);
        } else {
            $query->where($key, $value);
        }
    }
    
    return $this->applyTenantScope($query);
}
```

### **1.2 Implement Dark Mode in User Model**
- [x] **Add dark_mode field** to users table migration
- [x] **Add theme_preferences field** (JSON) for future customization
- [x] **Update User model** with proper casts and methods
- [x] **Create API endpoints** for theme management

**Migration Needed:**
```php
Schema::table('users', function (Blueprint $table) {
    $table->boolean('dark_mode')->default(false)->after('is_active');
    $table->json('theme_preferences')->nullable()->after('dark_mode');
    $table->string('language')->default('en')->after('theme_preferences');
});
```

### **1.3 Standardize Controller Responses**
- [x] **Create BaseController** with standard response methods
- [x] **Implement error handling** pattern across all controllers
- [x] **Add proper logging** for debugging
- [x] **Remove business logic** from controllers

---

## 🎉 FASE 1 COMPLETADA - 100% EXITOSA

### **✅ Logros Alcanzados:**
- [x] **BaseRepository Security Fixes** - SQL injection prevención
- [x] **Dark Mode Full Implementation** - Frontend + Backend + Database
- [x] **Controller Response Standardization** - BaseController + ApiResponse trait
- [x] **All Components Tested** - Funcionalidad validada

### **📈 Impacto del Progreso:**
1. **Security** - Repositorios seguros sin vulnerabilidades
2. **User Experience** - Dark mode persistente y consistente  
3. **API Quality** - Respuestas estandarizadas y predecibles
4. **Code Maintainability** - Patrones reutilizables y traits

---

## 🟡 HIGH - Phase 2: Architecture

### **2.1 Refactor Controller-Service-Repository Pattern**
- [x] **DashboardController** - Move logic to services
- [ ] **ProductController** - Implement proper service layer
- [ ] **Auth Controllers** - Standardize response format
- [ ] **ProfileController** - Add theme management endpoints

**Current Issues:**
- Controllers contain business logic
- Inconsistent response formats
- Missing error handling
- No proper tenant validation

### **2.2 Implement Tenant-Aware Repositories**
- [ ] **BaseRepository** - Add tenant scope methods
- [ ] **ProductRepository** - Implement tenant filtering
- [ ] **UserRepository** - Handle tenant relationships
- [ ] **CurrencyRateRepository** - Fix tenant isolation

**Required Methods:**
```php
interface TenantAwareRepositoryInterface
{
    public function forTenant(Tenant $tenant): self;
    public function currentTenant(): self;
    public function getAllForTenant(Tenant $tenant): Collection;
    public function createForTenant(array $data, Tenant $tenant): Model;
}
```

### **2.3 Create Essential Traits**
- [ ] **HasTenantScope** trait for models
- [ ] **ApiResponse** trait for controllers
- [ ] **DarkModePreferences** trait for models
- [ ] **MultitenantValidation** trait for requests

---

## 🟢 MEDIUM - Phase 3: Code Quality

### **3.1 Implement Service Layer Refactoring**
- [ ] **DashboardService** - Reduce dependencies, split responsibilities
- [ ] **ProductService** - Create new service for product logic
- [ ] **TenantService** - Enhance tenant management
- [ ] **UserService** - Add user preference management

**Service Pattern:**
```php
abstract class BaseService
{
    abstract protected function getRepository();
    abstract protected function validateData(array $data): array;
    
    protected function handleException(Exception $e, string $context): void
    {
        Log::error($context, [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'tenant' => tenant()?->id,
            'user' => auth()->id()
        ]);
    }
}
```

### **3.2 Create Request Validation Classes**
- [ ] **DashboardRequest** - Validate dashboard access
- [ ] **ProductRequest** - Product CRUD validation
- [ ] **ThemeRequest** - Theme preference validation
- [ ] **TenantRequest** - Tenant management validation

### **3.3 Implement API Resources**
- [ ] **UserResource** - Standardize user data output
- [ ] **ProductResource** - Product data formatting
- [ ] **TenantResource** - Tenant data representation
- [ ] **DashboardResource** - Dashboard data structure

---

## 🔵 LOW - Phase 4: Optimizations

### **4.1 Performance Optimizations**
- [ ] **Add database indexes** for tenant queries
- [ ] **Implement caching** for frequently accessed data
- [ ] **Optimize queries** to prevent N+1 problems
- [ ] **Add query logging** for monitoring

### **4.2 Enhanced Features**
- [ ] **Theme customization** - Advanced theme options
- [ ] **Tenant branding** - Enhanced branding config
- [ ] **User preferences** - Extended preference system
- [ ] **Audit logging** - Track important changes

---

## 🛠️ Traits Implementation Plan

### **Core Traits to Create:**

#### **1. HasTenantScope Trait**
```php
trait HasTenantScope
{
    public function scopeForTenant($query, Tenant $tenant)
    {
        return $query->where('tenant_id', $tenant->id);
    }
    
    public function scopeCurrentTenant($query)
    {
        if (tenant()) {
            return $query->where('tenant_id', tenant()->id);
        }
        return $query;
    }
    
    protected static function bootHasTenantScope()
    {
        static::creating(function ($model) {
            if (tenant() && !$model->tenant_id) {
                $model->tenant_id = tenant()->id;
            }
        });
    }
}
```

#### **2. ApiResponse Trait**
```php
trait ApiResponse
{
    protected function success($data = null, string $message = 'Success')
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
    }
    
    protected function error(string $message, int $code = 400, $data = null)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], $code);
    }
    
    protected function validationError($errors)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $errors
        ], 422);
    }
}
```

#### **3. DarkModePreferences Trait**
```php
trait DarkModePreferences
{
    public function getDarkModeAttribute(): bool
    {
        return $this->attributes['dark_mode'] ?? 
               config('app.default_dark_mode', false);
    }
    
    public function getThemePreferencesAttribute(): array
    {
        return $this->attributes['theme_preferences'] ?? 
               config('app.default_theme_preferences', []);
    }
    
    public function toggleDarkMode(): bool
    {
        $this->dark_mode = !$this->dark_mode;
        $this->save();
        
        // Log preference change
        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->log('Dark mode preference changed');
            
        return $this->dark_mode;
    }
    
    public function updateThemePreferences(array $preferences): array
    {
        $this->theme_preferences = array_merge(
            $this->theme_preferences,
            $preferences
        );
        $this->save();
        
        return $this->theme_preferences;
    }
}
```

#### **4. MultitenantValidation Trait**
```php
trait MultitenantValidation
{
    public function authorizeTenantAccess(Tenant $tenant): bool
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        return $user->tenants()->where('tenants.id', $tenant->id)->exists();
    }
    
    public function validateTenantOwnership(Model $model): bool
    {
        if (!tenant()) {
            return false;
        }
        
        return $model->tenant_id === tenant()->id;
    }
    
    public function getTenantValidationRules(): array
    {
        return [
            'tenant_id' => [
                'required',
                'exists:tenants,id',
                function ($attribute, $value, $fail) {
                    $tenant = Tenant::find($value);
                    if (!$this->authorizeTenantAccess($tenant)) {
                        $fail('You do not have access to this tenant.');
                    }
                }
            ]
        ];
    }
}
```

### **Usage Examples:**

#### **Model with Traits:**
```php
class Product extends Model
{
    use HasFactory, HasTenantScope, DarkModePreferences;
    
    protected $fillable = [
        'tenant_id', 'name', 'description', 'price', 'is_active'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'dark_mode' => 'boolean',
        'theme_preferences' => 'array'
    ];
}
```

#### **Controller with Traits:**
```php
class ProductController extends Controller
{
    use ApiResponse;
    
    public function index(ProductRequest $request)
    {
        try {
            $products = $this->productService
                ->getProductsForCurrentTenant($request->validated());
            
            return $this->success($products, 'Products retrieved successfully');
        } catch (Exception $e) {
            return $this->error('Failed to retrieve products', 500);
        }
    }
}
```

---

## 📅 Implementation Timeline

### **Week 1-2: Critical Issues**
- Fix BaseRepository security
- Implement dark mode in User model
- Standardize controller responses

### **Week 3-4: Architecture**
- Refactor controller-service-repository pattern
- Implement tenant-aware repositories
- Create essential traits

### **Week 5-6: Code Quality**
- Refactor service layer
- Create request validation classes
- Implement API resources

### **Week 7-8: Optimizations**
- Performance improvements
- Enhanced features
- Documentation and testing

---

## ✅ Completion Criteria

### **Phase 1 Complete When:**
- [ ] All security vulnerabilities fixed
- [ ] Dark mode fully functional
- [ ] Consistent API responses

### **Phase 2 Complete When:**
- [ ] Clean architecture implemented
- [ ] All repositories tenant-aware
- [ ] Essential traits created

### **Phase 3 Complete When:**
- [ ] All services follow patterns
- [ ] Request validation implemented
- [ ] API resources standardized

### **Phase 4 Complete When:**
- [ ] Performance optimized
- [ ] Enhanced features working
- [ ] Documentation complete

---

## 🎯 Success Metrics

### **Code Quality:**
- Zero security vulnerabilities
- Consistent code patterns
- Proper separation of concerns

### **Functionality:**
- Dark mode working end-to-end
- Multitenant isolation working
- API responses consistent

### **Maintainability:**
- Code follows established patterns
- Documentation up to date
- Easy to extend and modify

---

## 📝 Notes

### **Important Considerations:**
1. **Backup before major changes**
2. **Test in development environment first**
3. **Communicate changes to team**
4. **Update documentation as we go**
5. **Monitor performance after each phase**

### **Dependencies:**
- Phase 1 must be completed before Phase 2
- Traits should be created early for reuse
- Database migrations need careful planning

### **Testing Strategy:**
- Manual testing of dark mode functionality
- Verify tenant isolation works correctly
- Test API responses are consistent
- Validate security improvements

---

**Last Updated:** 2025-03-08
**Next Review:** 2025-03-15
