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
            $path = base_path("lang/{$locale}/{$module}.json");

            if (file_exists($path)) {
                $content = file_get_contents($path);
                $decoded = json_decode($content, true);
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
        // Forzar español como idioma por defecto
        // Solo cambiará si el usuario lo selecciona explícitamente (guardado en session)
        return session('locale', 'es');
    }
}
