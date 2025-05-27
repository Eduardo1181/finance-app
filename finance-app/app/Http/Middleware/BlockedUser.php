<?php

namespace App\Http\Middleware;

use Closure;

class BlockedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->is_blocked) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['blocked' => 'Seu acesso está bloqueado. Entre em contato com o suporte.']);
        }
        return $next($request);
    }
}
