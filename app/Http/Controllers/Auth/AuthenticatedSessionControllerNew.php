<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display login view.
     */
    public function create(Request $request): Response
    {
        // Get email from session or query parameter
        $email = session('login_email') || $request->get('email', '');
        
        // Check if email is currently locked
        $isLocked = $email ? LoginAttempt::isEmailLocked($email) : false;
        $lockoutTime = $isLocked ? LoginAttempt::getRemainingLockoutTime($email) : 0;
        
        // Get failed attempts count for display
        $failedAttempts = $email ? LoginAttempt::getFailedAttemptsCount($email, 60) : 0;
        $remainingAttempts = max(0, 5 - $failedAttempts);

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'loginAttempts' => $remainingAttempts,
            'maxAttempts' => 5,
            'lockoutTime' => $lockoutTime,
            'isLocked' => $isLocked,
            'email' => $email,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
