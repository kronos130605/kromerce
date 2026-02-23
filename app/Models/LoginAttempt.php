<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'attempted_at',
        'successful',
        'user_agent',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
        'successful' => 'boolean',
    ];

    /**
     * Get failed attempts count for email in last X minutes
     */
    public static function getFailedAttemptsCount(string $email, int $minutes = 15): int
    {
        return self::where('email', $email)
            ->where('successful', false)
            ->where('attempted_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Check if email is locked due to too many failed attempts
     */
    public static function isEmailLocked(string $email): bool
    {
        $recentAttempts = self::getFailedAttemptsCount($email, 60); // Last hour
        
        // Lock after 5 failed attempts in 1 hour
        if ($recentAttempts >= 5) {
            return true;
        }

        // Check if there's a recent lockout
        $lastFailedAttempt = self::where('email', $email)
            ->where('successful', false)
            ->orderBy('attempted_at', 'desc')
            ->first();

        if ($lastFailedAttempt && $recentAttempts >= 5) {
            $lockoutDuration = config('auth.lockout_duration', 15); // 15 minutes
            return $lastFailedAttempt->attempted_at->addMinutes($lockoutDuration) > now();
        }

        return false;
    }

    /**
     * Get remaining lockout time in seconds
     */
    public static function getRemainingLockoutTime(string $email): int
    {
        $lastFailedAttempt = self::where('email', $email)
            ->where('successful', false)
            ->orderBy('attempted_at', 'desc')
            ->first();

        if (!$lastFailedAttempt) {
            return 0;
        }

        $recentAttempts = self::getFailedAttemptsCount($email, 60);
        if ($recentAttempts < 5) {
            return 0;
        }

        $lockoutDuration = config('auth.lockout_duration', 15); // 15 minutes
        $lockoutUntil = $lastFailedAttempt->attempted_at->addMinutes($lockoutDuration);
        
        return max(0, $lockoutUntil->timestamp - now()->timestamp);
    }

    /**
     * Record a login attempt
     */
    public static function recordAttempt(
        string $email, 
        string $ipAddress, 
        bool $successful = false, 
        ?string $userAgent = null
    ): void {
        self::create([
            'email' => $email,
            'ip_address' => $ipAddress,
            'attempted_at' => now(),
            'successful' => $successful,
            'user_agent' => $userAgent,
        ]);

        // Clean old attempts (older than 24 hours)
        self::where('attempted_at', '<', now()->subHours(24))->delete();
    }

    /**
     * Clear successful attempts for email
     */
    public static function clearAttemptsForEmail(string $email): void
    {
        self::where('email', $email)->delete();
    }
}
