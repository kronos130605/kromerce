<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Lang;

class TranslationHelper
{
    public static function load(array|string $modules, ?string $locale = null): array
    {
        $locale = $locale ?? self::getLocale();

        if (is_string($modules)) {
            $modules = [$modules];
        }

        $translations = [];

        foreach ($modules as $module) {
            // Load JSON file directly since Laravel's Lang::get() expects PHP files
            $path = base_path("lang/{$locale}/{$module}.json");
            
            \Log::info("Loading translation", [
                'module' => $module,
                'path' => $path,
                'exists' => file_exists($path)
            ]);
            
            if (file_exists($path)) {
                $content = file_get_contents($path);
                $decoded = json_decode($content, true);
                
                \Log::info("Decoded JSON", [
                    'module' => $module,
                    'has_content' => !empty($decoded),
                    'keys' => array_keys($decoded ?? [])
                ]);
                
                // Extract nested content if the JSON has the module name as root key
                // e.g., {"dashboard": {...}} -> extract the inner object
                $translations[$module] = $decoded[$module] ?? $decoded;
            } else {
                $translations[$module] = [];
            }
        }

        return $translations;
    }

    public static function forPreset(string $preset, ?string $locale = null): array
    {
        $locale = $locale ?? self::getLocale();

        $presets = config('translations.presets');

        $modules = $presets[$preset] ?? [$preset, 'common'];

        return self::load($modules, $locale);
    }

    public static function getLocale(): string
    {
        return session('locale', config('app.locale', 'es'));
    }
}
