<?php

namespace App\Services\Currency;

use App\Models\CurrencySource;
use App\Services\Currency\Providers\Contracts\CurrencyProviderInterface;
use App\Services\Currency\Providers\ApiCurrencyProvider;
use App\Services\Currency\Providers\WebScraperProvider;
use App\Services\Currency\Providers\BccCubaProvider;
use App\Services\Currency\Providers\ElToqueProvider;
use Illuminate\Support\Facades\Crypt;

class CurrencyProviderFactory
{
    /**
     * Mapping of provider class names to actual class references.
     */
    protected static array $providerMap = [
        ApiCurrencyProvider::class => ApiCurrencyProvider::class,
        WebScraperProvider::class => WebScraperProvider::class,
        BccCubaProvider::class => BccCubaProvider::class,
        ElToqueProvider::class => ElToqueProvider::class,
    ];

    /**
     * Create provider instance from CurrencySource model.
     * Uses provider_class from source configuration.
     */
    public static function make(CurrencySource $source, ?array $configOverride = null): CurrencyProviderInterface
    {
        $credentials = self::resolveCredentials($source, $configOverride);
        $config = array_merge($source->config ?? [], $configOverride ?? []);
        $config['auth_type'] = $source->auth_type;

        // Use specific provider class if configured, otherwise fall back to type-based
        if (!empty($source->provider_class) && isset(self::$providerMap[$source->provider_class])) {
            $providerClass = self::$providerMap[$source->provider_class];
            return new $providerClass($source->base_url, $config, $credentials);
        }

        // Fallback to type-based selection
        return match ($source->type) {
            'web' => new WebScraperProvider($source->base_url, $config, $credentials),
            default => new ApiCurrencyProvider($source->base_url, $config, $credentials),
        };
    }

    /**
     * Create provider by source code.
     */
    public static function makeByCode(string $code, ?array $configOverride = null): ?CurrencyProviderInterface
    {
        $source = CurrencySource::where('code', $code)->where('is_active', true)->first();
        
        if (!$source) {
            return null;
        }

        return self::make($source, $configOverride);
    }

    /**
     * Resolve credentials with override support.
     */
    protected static function resolveCredentials(CurrencySource $source, ?array $configOverride): ?array
    {
        $credentials = [];

        // Start with source default credentials
        if ($source->default_credentials) {
            $decrypted = $source->getDecryptedCredentials();
            if ($decrypted) {
                $credentials = $decrypted;
            }
        }

        // Override with store-specific credentials from config_override
        if (!empty($configOverride['api_key'])) {
            $credentials['api_key'] = $configOverride['api_key'];
        }
        
        if (!empty($configOverride['username'])) {
            $credentials['username'] = $configOverride['username'];
        }
        
        if (!empty($configOverride['password'])) {
            $credentials['password'] = $configOverride['password'];
        }
        
        if (!empty($configOverride['token'])) {
            $credentials['token'] = $configOverride['token'];
        }

        return empty($credentials) ? null : $credentials;
    }

    /**
     * Get available provider types.
     */
    public static function getProviderTypes(): array
    {
        return [
            'api' => 'API REST',
            'web' => 'Web Scraping',
        ];
    }

    /**
     * Get available provider classes.
     */
    public static function getProviderClasses(): array
    {
        return [
            'api' => ApiCurrencyProvider::class,
            'web' => WebScraperProvider::class,
            'bcc-cuba' => BccCubaProvider::class,
            'eltoque-cuba' => ElToqueProvider::class,
        ];
    }

    /**
     * Test a source connection.
     */
    public static function testSource(CurrencySource $source, ?array $configOverride = null): array
    {
        $provider = self::make($source, $configOverride);
        $result = $provider->testConnection();
        
        // Update source statistics
        if ($result['success']) {
            $source->recordSuccess();
        } else {
            $source->recordFailure($result['message']);
        }

        return $result;
    }
}
