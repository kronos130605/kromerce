---
description: Backend Development Standards
---

# Backend Development Standards

## 🎯 Architecture Philosophy

### **Core Principles**
1. **Clean Architecture**: Controllers → Services → Repositories → Models
2. **Single Responsibility**: Each class has one reason to change
3. **Dependency Injection**: All dependencies injected via constructor
4. **Tenant Isolation**: All tenant-aware data properly isolated
5. **Code Reuse**: Use traits for shared functionality

### **Technology Stack**
- **Framework**: Laravel 12.x
- **Database**: MySQL/PostgreSQL compatible
- **Multitenant**: Stancl Tenancy
- **Authentication**: Laravel Fortify + Spatie Permissions
- **API**: RESTful with Inertia.js for frontend

## 🏗️ Directory Structure

```
app/
├── Http/
│   ├── Controllers/          # HTTP request handling
│   │   ├── Auth/            # Authentication controllers
│   │   ├── Dashboard/       # Dashboard controllers
│   │   └── Api/             # API endpoints
│   ├── Requests/            # Form request validation
│   └── Resources/           # API resource transformation
├── Services/               # Business logic layer
├── Repositories/           # Data access layer
├── Models/                # Eloquent models
├── Enums/                 # Enumerations
├── Traits/               # Reusable traits
├── Exceptions/            # Custom exceptions
└── Jobs/                 # Queue jobs
```

## 📋 Controller Standards

### **Base Controller Pattern**
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Traits\ApiResponse;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponse;
    
    protected function validateTenant(Tenant $tenant = null): ?Tenant
    {
        if (!$tenant) {
            $tenant = tenant();
        }
        
        if (!$tenant) {
            throw new UnauthorizedException('No tenant context found');
        }
        
        return $tenant;
    }
}
```

### **Controller Implementation Pattern**
```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}
    
    /**
     * Display a listing of the resource.
     */
    public function index(ProductRequest $request): JsonResponse
    {
        try {
            $tenant = $this->validateTenant();
            $products = $this->productService->getProductsForTenant(
                $tenant,
                $request->validated()
            );
            
            return $this->success($products, 'Products retrieved successfully');
        } catch (Exception $e) {
            return $this->error('Failed to retrieve products', 500);
        }
    }
    
    /**
     * Store a newly created resource.
     */
    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $tenant = $this->validateTenant();
            $product = $this->productService->createProductForTenant(
                $tenant,
                $request->validated()
            );
            
            return $this->success($product, 'Product created successfully', 201);
        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (Exception $e) {
            return $this->error('Failed to create product', 500);
        }
    }
}
```

### **Controller Rules**
- ✅ **Only HTTP handling** - No business logic
- ✅ **Dependency injection** - All dependencies via constructor
- ✅ **Standard responses** - Use ApiResponse trait
- ✅ **Proper validation** - Use Form Request classes
- ✅ **Error handling** - Try-catch with proper responses
- ❌ **No direct database access** - Use repositories
- ❌ **No business logic** - Delegate to services
- ❌ **No complex calculations** - Move to services

## 🔧 Service Standards

### **Base Service Pattern**
```php
<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;

abstract class BaseService
{
    /**
     * Get the repository for this service.
     */
    abstract protected function getRepository();
    
    /**
     * Validate data for the service operation.
     */
    abstract protected function validateData(array $data): array;
    
    /**
     * Validate tenant access.
     */
    protected function validateTenant(?Tenant $tenant = null): ?Tenant
    {
        if (!$tenant) {
            $tenant = tenant();
        }
        
        if (!$tenant) {
            throw new ServiceException('No tenant context found');
        }
        
        return $tenant;
    }
    
    /**
     * Handle service exceptions.
     */
    protected function handleException(Exception $e, string $context): void
    {
        Log::error($context, [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'tenant' => tenant()?->id,
            'user' => auth()->id()
        ]);
        
        throw new ServiceException($context . ' failed');
    }
    
    /**
     * Validate user access to tenant.
     */
    protected function validateUserTenantAccess(User $user, Tenant $tenant): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        return $user->tenants()->where('tenants.id', $tenant->id)->exists();
    }
}
```

### **Service Implementation Pattern**
```php
<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService extends BaseService
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}
    
    protected function getRepository(): ProductRepository
    {
        return $this->productRepository;
    }
    
    protected function validateData(array $data): array
    {
        return validator($data, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ])->validate();
    }
    
    /**
     * Get products for a tenant.
     */
    public function getProductsForTenant(
        Tenant $tenant, 
        array $filters = []
    ): LengthAwarePaginator {
        try {
            return $this->productRepository
                ->forTenant($tenant)
                ->withFilters($filters)
                ->paginate(15);
        } catch (Exception $e) {
            $this->handleException($e, 'Product retrieval');
        }
    }
    
    /**
     * Create product for tenant.
     */
    public function createProductForTenant(Tenant $tenant, array $data): Product
    {
        try {
            $validatedData = $this->validateData($data);
            $validatedData['tenant_id'] = $tenant->id;
            
            return $this->productRepository->create($validatedData);
        } catch (Exception $e) {
            $this->handleException($e, 'Product creation');
        }
    }
}
```

### **Service Rules**
- ✅ **Business logic only** - No HTTP concerns
- ✅ **Single responsibility** - One service per domain
- ✅ **Dependency injection** - All dependencies via constructor
- ✅ **Error handling** - Consistent exception handling
- ✅ **Validation** - Input validation before processing
- ❌ **No HTTP responses** - Return domain objects
- ❌ **No direct database access** - Use repositories
- ❌ **No HTTP-specific logic** - Framework agnostic

## 🗄️ Repository Standards

### **Base Repository Pattern**
```php
<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Tenant;

abstract class BaseRepository
{
    protected Model $model;
    protected array $allowedFields = [];
    
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    
    /**
     * Find model by ID.
     */
    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }
    
    /**
     * Find model by field.
     */
    public function findBy(string $field, mixed $value): ?Model
    {
        return $this->model->where($field, $value)->first();
    }
    
    /**
     * Get all records.
     */
    public function all(): Collection
    {
        return $this->model->all();
    }
    
    /**
     * Create new record.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }
    
    /**
     * Update record.
     */
    public function update(int $id, array $data): bool
    {
        return $this->model->find($id)?->update($data) ?? false;
    }
    
    /**
     * Delete record.
     */
    public function delete(int $id): bool
    {
        return $this->model->find($id)?->delete() ?? false;
    }
    
    /**
     * Apply tenant scope to query.
     */
    protected function applyTenantScope(Builder $query, ?Tenant $tenant = null): Builder
    {
        if ($tenant) {
            return $query->where('tenant_id', $tenant->id);
        }
        
        if (tenant()) {
            return $query->where('tenant_id', tenant()->id);
        }
        
        return $query;
    }
    
    /**
     * Apply filters to query.
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            if (!in_array($field, $this->allowedFields)) {
                continue;
            }
            
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }
        
        return $query;
    }
    
    /**
     * Paginate results.
     */
    protected function paginate(Builder $query, int $perPage = 15): LengthAwarePaginator
    {
        return $query->paginate($perPage);
    }
}
```

### **Repository Implementation Pattern**
```php
<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository extends BaseRepository
{
    protected array $allowedFields = [
        'name', 'description', 'price', 'is_active', 'category_id'
    ];
    
    public function __construct(Product $product)
    {
        parent::__construct($product);
    }
    
    /**
     * Get products for tenant with filters.
     */
    public function forTenant(Tenant $tenant): self
    {
        $this->query = $this->model->newQuery();
        $this->query = $this->applyTenantScope($this->query, $tenant);
        
        return $this;
    }
    
    /**
     * Apply filters to products query.
     */
    public function withFilters(array $filters): self
    {
        $this->query = $this->applyFilters($this->query, $filters);
        
        return $this;
    }
    
    /**
     * Get paginated results.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->paginate($this->query, $perPage);
    }
    
    /**
     * Get active products only.
     */
    public function active(): self
    {
        $this->query->where('is_active', true);
        
        return $this;
    }
    
    /**
     * Search products by name.
     */
    public function search(string $term): self
    {
        $this->query->where('name', 'like', "%{$term}%");
        
        return $this;
    }
}
```

### **Repository Rules**
- ✅ **Data access only** - No business logic
- ✅ **Query building** - Complex queries here
- ✅ **Tenant scoping** - Automatic tenant filtering
- ✅ **Security** - Input validation and sanitization
- ✅ **Performance** - Proper indexing and optimization
- ❌ **No HTTP concerns** - Return domain objects
- ❌ **No business rules** - Move to services
- ❌ **No validation logic** - Input validation only

## 🎭 Model Standards

### **Model Pattern with Traits**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasTenantScope;
use App\Traits\DarkModePreferences;

class Product extends Model
{
    use HasFactory, HasTenantScope, DarkModePreferences;
    
    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'price',
        'is_active',
        'category_id'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'dark_mode' => 'boolean',
        'theme_preferences' => 'array'
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    
    /**
     * Get the tenant that owns the product.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
    
    /**
     * Get the category for the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }
    
    /**
     * Scope to get active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2);
    }
}
```

### **Model Rules**
- ✅ **Define relationships** - All model relationships
- ✅ **Use traits** - Reuse common functionality
- ✅ **Proper casting** - Define attribute casts
- ✅ **Fillable fields** - Mass assignment protection
- ✅ **Scopes** - Common query scopes
- ✅ **Accessors/Mutators** - Custom attribute formatting
- ❌ **No business logic** - Move to services
- ❌ **No HTTP concerns** - Framework agnostic

## 🎨 Traits Library

### **1. HasTenantScope Trait**
```php
<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;

trait HasTenantScope
{
    /**
     * Boot the trait.
     */
    protected static function bootHasTenantScope()
    {
        static::creating(function ($model) {
            if (tenant() && !$model->tenant_id) {
                $model->tenant_id = tenant()->id;
            }
        });
    }
    
    /**
     * Scope to get records for specific tenant.
     */
    public function scopeForTenant(Builder $query, Tenant $tenant): Builder
    {
        return $query->where('tenant_id', $tenant->id);
    }
    
    /**
     * Scope to get records for current tenant.
     */
    public function scopeCurrentTenant(Builder $query): Builder
    {
        if (tenant()) {
            return $query->where('tenant_id', tenant()->id);
        }
        
        return $query;
    }
}
```

### **2. ApiResponse Trait**
```php
<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return success response.
     */
    protected function success(
        $data = null, 
        string $message = 'Success', 
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }
    
    /**
     * Return error response.
     */
    protected function error(
        string $message, 
        int $code = 400, 
        $data = null
    ): JsonResponse {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], $code);
    }
    
    /**
     * Return validation error response.
     */
    protected function validationError($errors): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $errors
        ], 422);
    }
}
```

### **3. DarkModePreferences Trait**
```php
<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait DarkModePreferences
{
    /**
     * Get dark mode preference.
     */
    public function getDarkModeAttribute(): bool
    {
        return $this->attributes['dark_mode'] ?? 
               config('app.default_dark_mode', false);
    }
    
    /**
     * Get theme preferences.
     */
    public function getThemePreferencesAttribute(): array
    {
        return $this->attributes['theme_preferences'] ?? 
               config('app.default_theme_preferences', []);
    }
    
    /**
     * Toggle dark mode.
     */
    public function toggleDarkMode(): bool
    {
        $this->dark_mode = !$this->dark_mode;
        $this->save();
        
        return $this->dark_mode;
    }
    
    /**
     * Update theme preferences.
     */
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

## 📝 Request Validation Standards

### **Form Request Pattern**
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Add authorization logic if needed
    }
    
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0|max:999999.99',
            'is_active' => 'sometimes|boolean',
            'category_id' => 'nullable|exists:product_categories,id'
        ];
    }
    
    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required',
            'price.required' => 'Product price is required',
            'price.numeric' => 'Price must be a valid number',
            'category_id.exists' => 'Selected category does not exist'
        ];
    }
}
```

## 🔄 API Resource Standards

### **API Resource Pattern**
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'is_active' => $this->is_active,
            'category' => new ProductCategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
```

---

## 🎯 Quality Checklist

### **For Every New Feature:**
- [ ] Controller uses dependency injection
- [ ] Service handles business logic
- [ ] Repository handles data access
- [ ] Model uses appropriate traits
- [ ] Request validation implemented
- [ ] API resource for responses
- [ ] Tenant isolation verified
- [ ] Error handling implemented
- [ ] Logging added for debugging
- [ ] Documentation updated

### **Security Checklist:**
- [ ] Input validation implemented
- [ ] SQL injection prevention
- [ ] Tenant isolation verified
- [ ] Authorization checks added
- [ ] Sensitive data hidden
- [ ] Error messages sanitized

### **Performance Checklist:**
- [ ] Database indexes added
- [ ] Queries optimized
- [ ] N+1 problems prevented
- [ ] Caching implemented where needed
- [ ] Pagination for large datasets

---

**Last Updated:** 2025-03-08
**Next Review:** 2025-03-15