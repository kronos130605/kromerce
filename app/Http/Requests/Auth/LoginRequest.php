<?php

namespace App\Http\Requests\Auth;

use App\Models\LoginAttempt;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get validation rules that apply to request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $email = $this->string('email');
        $ipAddress = $this->ip();
        $userAgent = $this->userAgent();

        // Check if email is locked
        if (LoginAttempt::isEmailLocked($email)) {
            $remainingTime = LoginAttempt::getRemainingLockoutTime($email);
            
            throw ValidationException::withMessages([
                'email' => trans('auth.account_locked', [
                    'minutes' => ceil($remainingTime / 60),
                ]),
            ]);
        }

        // Attempt authentication
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // Record failed attempt
            LoginAttempt::recordAttempt($email, $ipAddress, false, $userAgent);
            
            // Check if this attempt triggers a lockout
            if (LoginAttempt::isEmailLocked($email)) {
                $remainingTime = LoginAttempt::getRemainingLockoutTime($email);
                
                throw ValidationException::withMessages([
                    'email' => trans('auth.account_locked', [
                        'minutes' => ceil($remainingTime / 60),
                    ]),
                ]);
            }

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Record successful attempt and clear previous failures
        LoginAttempt::recordAttempt($email, $ipAddress, true, $userAgent);
        LoginAttempt::clearAttemptsForEmail($email);
    }

    /**
     * Get the rate limiting throttle key for request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')));
    }
}
