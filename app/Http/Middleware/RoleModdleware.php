<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

class RoleModdleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next,$role= null)
    {
        if (auth()->user()->roles()->where('name', 'super_admin')->count()) {
            return $next($request);
        }

        if (!auth()->user()->hasRole($role)) {
            throw new UnauthorizedException();
        }

        return $next($request);
    }
}
