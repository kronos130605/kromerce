<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserThemeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserThemeController extends Controller
{
    /**
     * Get user theme preferences.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            return $this->success([
                'dark_mode' => $user->dark_mode,
                'theme_preferences' => $user->theme_preferences,
                'language' => $user->language,
            ], 'Theme preferences retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve theme preferences', 500);
        }
    }
    
    /**
     * Toggle dark mode.
     */
    public function toggleDarkMode(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $newDarkMode = $user->toggleDarkMode();
            
            return $this->success([
                'dark_mode' => $newDarkMode
            ], 'Dark mode toggled successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to toggle dark mode', 500);
        }
    }
    
    /**
     * Update theme preferences.
     */
    public function updateThemePreferences(UserThemeRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $preferences = $user->updateThemePreferences($request->validated());
            
            return $this->success([
                'theme_preferences' => $preferences
            ], 'Theme preferences updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update theme preferences', 500);
        }
    }
    
    /**
     * Update language preference.
     */
    public function updateLanguage(UserThemeRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $language = $user->setLanguage($request->validated()['language']);
            
            return $this->success([
                'language' => $language
            ], 'Language updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update language', 500);
        }
    }
    
    /**
     * Reset theme preferences to defaults.
     */
    public function resetTheme(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $user->update([
                'dark_mode' => false,
                'theme_preferences' => null,
                'language' => 'en'
            ]);
            
            return $this->success([
                'dark_mode' => false,
                'theme_preferences' => [],
                'language' => 'en'
            ], 'Theme preferences reset successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to reset theme preferences', 500);
        }
    }
}
