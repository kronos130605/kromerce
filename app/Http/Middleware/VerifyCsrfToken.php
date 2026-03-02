<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // No excluir rutas de auth por seguridad
        // Si necesitas excluir para debugging, añade temporalmente:
        // 'login',
        // 'register', 
        // 'logout',
    ];
}
