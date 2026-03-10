---
description: Repository Factory Pattern Implementation
---

# Repository Factory Pattern Implementation

## Overview
This workflow describes how to implement and use the Repository Factory pattern in the application for clean dependency injection and repository management.

## When to Use This Pattern

- **New Repository Creation**: When creating new repositories
- **Service Updates**: When updating services to use repositories
- **Dependency Management**: When managing complex repository dependencies
- **Testing**: When mocking repositories for unit tests

## Implementation Steps

### 1. Create New Repository

```bash
# Create repository file
touch app/Repositories/NewRepository.php
```

```php
<?php

namespace App\Repositories;

use App\Models\NewModel;
use App\Repositories\BaseRepository;

class NewRepository extends BaseRepository
{
    public function __construct(NewModel $model)
    {
        parent::__construct($model);
    }

    // Add your custom methods here
}
```

### 2. Update RepositoryFactory

Add the new repository to the `RepositoryFactory`:

```php
// In app/Factories/RepositoryFactory.php

public static function make(string $repositoryClass): BaseRepository
{
    return match ($repositoryClass) {
        ProductRepository::class => new ProductRepository(new Product()),
        ProductCategoryRepository::class => new ProductCategoryRepository(new ProductCategory()),
        ProductTagRepository::class => new ProductTagRepository(new ProductTag()),
        NewRepository::class => new NewRepository(new NewModel()), // Add this line
        default => throw new \InvalidArgumentException("Repository {$repositoryClass} is not supported")
    };
}

public static function newRepository(): NewRepository
{
    return self::make(NewRepository::class);
}
```

### 3. Update AppServiceProvider

Register the new repository in `AppServiceProvider`:

```php
// In app/Providers/AppServiceProvider.php

public function register(): void
{
    // Register RepositoryFactory as singleton
    $this->app->singleton(App\Factories\RepositoryFactory::class, function ($app) {
        return new App\Factories\RepositoryFactory();
    });
    
    // Register repositories using factory
    $this->app->singleton(NewRepository::class, function ($app) {
        return $app->make(App\Factories\RepositoryFactory::class)->newRepository();
    });
}
```

### 4. Update Services to Use Factory

Update service constructors to use the factory:

```php
// In app/Services/YourService.php

class YourService
{
    public function __construct(
        private RepositoryFactory $repositoryFactory
    ) {}

    public function someMethod()
    {
        $repository = $this->repositoryFactory->newRepository();
        // Use repository
    }
}
```

## Usage Examples

### Direct Factory Usage

```php
$factory = app(RepositoryFactory::class);
$productRepo = $factory->productRepository();
$categoryRepo = $factory->productCategoryRepository();
```

### In Controllers

```php
class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}
}
```

### In Services

```php
class ProductService
{
    public function __construct(
        private RepositoryFactory $repositoryFactory
    ) {}

    public function getProducts()
    {
        return $this->repositoryFactory->productRepository()->all();
    }
}
```

## Benefits

1. **Clean Dependencies**: Single factory dependency instead of multiple repositories
2. **Consistent Instantiation**: All repositories created the same way
3. **Easy Testing**: Can mock the factory instead of individual repositories
4. **Centralized Management**: All repository creation in one place
5. **Type Safety**: Strong typing with specific repository methods
6. **Scalability**: Easy to add new repositories

## Testing

### Mocking the Factory

```php
// In your tests
$mockFactory = $this->createMock(RepositoryFactory::class);
$mockFactory->method('productRepository')
    ->willReturn($mockProductRepository);

$service = new ProductService($mockFactory);
```

### Factory Testing

```php
// Test factory creation
$factory = new RepositoryFactory();
$repository = $factory->productRepository();
$this->assertInstanceOf(ProductRepository::class, $repository);
```

## Best Practices

1. **Always use the factory** for repository creation
2. **Register repositories in AppServiceProvider** for dependency injection
3. **Keep factory methods specific** (e.g., `productRepository()` instead of generic `make()`)
4. **Add type hints** for all factory methods
5. **Document new repositories** in the factory match statement
6. **Use repositories in services** through the factory, not direct instantiation

## Troubleshooting

### Common Issues

1. **"Repository not supported" error**: Add repository to factory match statement
2. **Dependency injection fails**: Ensure repository is registered in AppServiceProvider
3. **Model not found**: Check import statements and model existence

### Debug Commands

```bash
# Clear caches
wsl php artisan config:clear
wsl php artisan route:clear

# Check service registration
wsl php artisan tinker
> app(RepositoryFactory::class)
> app(ProductRepository::class)
```

## Migration from Direct Injection

To migrate existing services:

1. Add RepositoryFactory to service constructor
2. Replace direct repository usage with factory calls
3. Update AppServiceProvider registration
4. Test functionality
5. Remove old repository dependencies
