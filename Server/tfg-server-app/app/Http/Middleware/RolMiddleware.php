<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$rols): Response
    {
        $user = auth()->user();

        if (!$user || !$user->rol) {
            abort(403);
        }

        if (!in_array($user->rol->slug, $rols)) {
            return redirect()->route('dashboard.index');
        }

        return $next($request);
    }
}
