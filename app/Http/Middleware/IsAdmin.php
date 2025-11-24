<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se está logado E se é admin
        if (Auth::check() && Auth::user()->isAdmin()) {
            return $next($request);
        }

        // Se não for, erro 403
        abort(403, 'Unauthorized.');
    }
}
