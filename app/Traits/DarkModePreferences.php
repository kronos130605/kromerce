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
        $preferences = $this->attributes['theme_preferences'] ?? 
                     config('app.default_theme_preferences', []);
        
        // Ensure we always return an array
        if (is_string($preferences)) {
            return json_decode($preferences, true) ?? [];
        }
        
        return (array) $preferences;
    }
    
    /**
     * Get language preference.
     */
    public function getLanguageAttribute(): string
    {
        return $this->attributes['language'] ?? 
               config('app.fallback_locale', 'en');
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
    
    /**
     * Set language preference.
     */
    public function setLanguage(string $language): string
    {
        $this->language = $language;
        $this->save();
        
        return $this->language;
    }
    
    /**
     * Get user preference for dark mode API response.
     */
    public function getDarkModePreference(): array
    {
        return [
            'dark_mode' => $this->dark_mode,
            'theme_preferences' => $this->theme_preferences,
            'language' => $this->language,
        ];
    }
}
