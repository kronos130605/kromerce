<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (\Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, \Throwable $e)
    {
        // Handle token mismatch (419 Page Expired)
        if ($e instanceof TokenMismatchException) {
            if ($request->expectsJson() || $request->header('X-Inertia')) {
                return Inertia::render('Auth/Login', [
                    'status' => 'Tu sesión ha expirado. Por favor inicia sesión nuevamente.',
                    'canResetPassword' => true,
                    'loginAttempts' => 5,
                    'maxAttempts' => 5,
                    'lockoutTime' => 0,
                    'isLocked' => false,
                ])
                ->setStatusCode(419);
            }

            return redirect()->route('login')
                ->with('status', 'Tu sesión ha expirado. Por favor inicia sesión nuevamente.');
        }

        return parent::render($request, $e);
    }
}
