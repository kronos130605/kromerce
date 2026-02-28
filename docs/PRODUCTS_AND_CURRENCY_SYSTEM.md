# Products and Currency System

## Overview

This document describes the complete products and currency system implemented for Kromerce, including multi-currency support with historical rate tracking.

## Features

### 💰 Currency System
- **Multi-currency support** with automatic conversion
- **Historical rate tracking** (1-2 years retention)
- **Custom rates per business** with global fallback
- **Daily automatic updates** from external APIs
- **Rate history analysis** for financial reporting

### 📦 Product Management
- **Simple pricing model** with automatic multi-currency conversion
- **Product variants** with individual pricing
- **Category hierarchy** with unlimited levels
- **Tag system** for flexible organization
- **Image management** with primary image support
- **Inventory tracking** with low stock alerts
- **Sale pricing** with flexible discount types

### 🎯 Advanced Features
- **Historical cost tracking** for accurate financial analysis
- **Price change history** with reason tracking
- **Margin calculation** with cost tracking
- **SEO optimization** with meta tags and structured data
- **Shipping configuration** with weight and dimensions
- **Tax configuration** per product

## Database Structure

### Core Tables

#### Currency Management
- `business_currency_configs` - Per-business currency settings
- `currency_rates_global` - Global default rates
- `currency_rates_business` - Custom business rates
- `currency_rate_updates` - Update history and tracking

#### Product Management
- `products` - Main product table with pricing
- `product_categories` - Hierarchical categories
- `product_tags` - Product tagging system
- `product_images` - Product image management
- `product_variants` - Product variants
- `product_attributes` - Variant attributes
- `product_attribute_values` - Attribute values

#### Relationships
- `product_category_product` - Many-to-many relationship
- `product_product_tag` - Many-to-many relationship
- `product_price_history` - Price change tracking

## API Endpoints

### Product Routes
```
GET    /products                    - List products
POST   /products                    - Create product
GET    /products/{product}          - Show product
PUT    /products/{product}          - Update product
DELETE /products/{product}          - Delete product
POST   /products/{product}/duplicate - Duplicate product
GET    /products/{product}/prices   - Get product prices
```

### Currency Routes
```
GET    /currency                    - Currency configuration
PUT    /currency/config             - Update config
GET    /currency/rates              - Current rates
PUT    /currency/rates/custom       - Update custom rates
GET    /currency/rates/history      - Rate history
POST   /currency/rates/reset        - Reset to global
```

## Services

### CurrencyRateService
Handles all currency rate operations:
- Daily rate updates from APIs
- Historical rate management
- Rate conversion calculations
- Business-specific rate handling

### ProductPricingService
Manages product pricing across currencies:
- Multi-currency price calculation
- Historical cost conversion
- Margin calculations
- Price change tracking

## Configuration

### Business Currency Config
```php
[
    'default_currency' => 'USD',
    'display_currencies' => ['USD', 'EUR', 'GBP', 'COP', 'MXN'],
    'use_custom_rates' => false,
    'auto_update_rates' => true,
    'rate_update_frequency' => 'daily',
    'historical_retention_years' => 2,
]
```

### Product Pricing Structure
```php
[
    'base_currency' => 'USD',
    'base_price' => 29.99,
    'base_sale_price' => 24.99,  // Optional
    'cost_price' => 15.50,        // Optional
    'track_cost' => true,
    'historical_cost' => [
        'amount' => 15.50,
        'currency' => 'USD',
        'rate' => 1.0,
        'date' => '2024-01-20'
    ]
]
```

## Console Commands

### Currency Management
```bash
# Update daily rates
php artisan currency:update-rates

# Clean up old rates (dry run)
php artisan currency:cleanup --dry-run

# Actually clean up old rates
php artisan currency:cleanup
```

## Usage Examples

### Creating a Product
```php
$product = Product::create([
    'name' => 'Wireless Headphones',
    'base_price' => 29.99,
    'base_currency' => 'USD',
    'cost_price' => 15.50,
    'track_cost' => true,
    'manage_stock' => true,
    'stock_quantity' => 100,
]);
```

### Getting Prices in Different Currencies
```php
$prices = $product->getCalculatedPrices();
// Returns:
[
    'USD' => [
        'price' => 29.99,
        'sale_price' => 24.99,
        'cost_price' => 15.50,
        'margin' => 48.3,
        'symbol' => '$',
        'flag' => '🇺🇸'
    ],
    'EUR' => [
        'price' => 27.59,
        'sale_price' => 23.03,
        'cost_price' => 14.30,
        'margin' => 48.3,
        'symbol' => '€',
        'flag' => '🇪🇺'
    ]
]
```

### Converting Historical Costs
```php
$historicalCost = $product->getCalculatedPrices()['EUR']['historical_cost'];
// Returns:
[
    'historical_amount' => 14.30,
    'historical_rate' => 0.92,
    'historical_date' => '2024-01-20',
    'current_amount' => 14.50,
    'current_rate' => 0.93,
    'difference' => 0.20,
    'difference_percentage' => 1.4
]
```

## Rate Updates

### Automatic Updates
The system automatically updates rates daily using:
1. **Primary**: External API (OpenExchangeRates, CurrencyLayer, etc.)
2. **Fallback**: Previous day's rates
3. **Business-specific**: Custom rates if configured

### Manual Updates
Businesses can override global rates with custom values:
- Manual rate entry
- Bulk rate import
- API-based updates

## Historical Analysis

### Rate History Tracking
- **Global rates**: 2 years retention by default
- **Business rates**: Configurable retention (1-10 years)
- **Automatic cleanup**: Scheduled cleanup of old data

### Financial Reporting
- **Accurate cost tracking**: Using historical rates
- **Margin analysis**: Real-time margin calculations
- **Profit reporting**: Based on actual costs at time of purchase

## Security & Performance

### Rate Caching
- **In-memory caching**: 5-minute cache for calculated prices
- **Database optimization**: Indexed queries for rate lookups
- **Background processing**: Async rate updates

### Access Control
- **Tenant isolation**: Each business only sees their data
- **Rate permissions**: Granular control over rate management
- **Audit trail**: Complete history of rate changes

## Integration Points

### External APIs
- **Currency rate APIs**: Configurable API endpoints
- **Fallback mechanisms**: Multiple API support
- **Rate validation**: Automatic validation of received rates

### Future Integrations
- **Accounting systems**: Export financial data
- **Marketplaces**: Sync products and prices
- **Payment processors**: Multi-currency payment support

## Best Practices

### Product Management
1. **Use base currency pricing**: Set prices in your business's base currency
2. **Enable cost tracking**: For accurate margin calculations
3. **Regular rate updates**: Keep rates current for accurate pricing
4. **Historical accuracy**: Preserve historical cost data for analysis

### Currency Management
1. **Configure retention periods**: Based on business requirements
2. **Monitor rate updates**: Check update success/failure logs
3. **Backup custom rates**: Export important custom rate data
4. **Test conversions**: Verify rate calculations are accurate

## Troubleshooting

### Common Issues
1. **Missing rates**: Check API connectivity and fallback mechanisms
2. **Incorrect conversions**: Verify currency codes and rate formats
3. **Performance issues**: Check caching and database indexes
4. **Historical data gaps**: Ensure regular rate updates

### Debug Tools
- **Rate update logs**: Check currency_rate_updates table
- **Conversion testing**: Use built-in conversion methods
- **Performance monitoring**: Track calculation times
- **Data validation**: Verify rate accuracy

## Migration Notes

### From Single Currency
1. **Set base currency**: Configure business default currency
2. **Migrate prices**: Update existing products with base currency
3. **Configure display currencies**: Set which currencies to show
4. **Test conversions**: Verify all calculations work correctly

### Data Import
1. **Rate history**: Import historical rate data if available
2. **Product costs**: Update historical cost information
3. **Category hierarchy**: Import category structures
4. **Image assets**: Migrate product images

This system provides a comprehensive foundation for multi-currency e-commerce with accurate financial tracking and flexible pricing management.
