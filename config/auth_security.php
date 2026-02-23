<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Lockout Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the lockout settings for authentication attempts.
    | This includes the number of failed attempts before lockout and the
    | duration of the lockout in minutes.
    |
    */

    'max_attempts' => env('AUTH_MAX_ATTEMPTS', 5),
    'lockout_duration' => env('AUTH_LOCKOUT_DURATION', 15), // 15 minutes
    'decay_minutes' => env('AUTH_DECAY_MINUTES', 60), // 1 hour window for counting attempts
    
    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Additional security settings for authentication
    |
    */
    
    'track_ip' => env('AUTH_TRACK_IP', true),
    'track_user_agent' => env('AUTH_TRACK_USER_AGENT', true),
    'cleanup_old_attempts' => env('AUTH_CLEANUP_OLD_ATTEMPTS', true),
    'cleanup_hours' => env('AUTH_CLEANUP_HOURS', 24),
];
